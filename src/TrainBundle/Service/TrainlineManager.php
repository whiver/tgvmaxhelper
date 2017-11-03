<?php
namespace TrainBundle\Service;

use GuzzleHttp;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use TrainBundle\Entity\OrderTrip;
use TrainBundle\Entity\Trip;
use TrainBundle\Entity\UserCard;

class TrainlineManager
{
    const API_URL = "https://www.trainline.fr/api/v5_1";

    private $em;

    public function __construct(\Swift_Mailer $mailer, $templating, EntityManager $em, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Automate Booking System
     *
     * @param $trip
     */
    public function bookingTrain($trip) {

        /**
         * Create if order exist for this trip
         * If not exist create it
         */
        $trip = $this->em->getRepository(Trip::class)->findOneById($trip->getId());
        if (!$trip->getOrder()) {
            // Generate Passenger UUID
            $passengerId = $this->gen_uuid();
            $order = new OrderTrip();
            $order->setUser($trip->getUser());
            $order->setPassengerUuid($passengerId);
            $trip->setOrder($order);
            $this->em->persist($order);
            $this->em->persist($trip);
        } else {
            // Generate Passenger UUID
            $passengerId = $this->gen_uuid();
            $order = $trip->getOrder();
            $order->setUser($trip->getUser());
            $order->setPassengerUuid($passengerId);
            $trip->setOrder($order);
            $this->em->persist($order);
            $this->em->persist($trip);
        }

        /**
         * Retrieve TGV Max card from User
         * Create passenger from User
         */
        $card = $this->em->getRepository(UserCard::class)->findOneByUser($trip->getUser());
        $passenger = array(array("id" => $passengerId, "label" => $this->getTypeByAge($trip->getUser()->getAge()), "age" => $trip->getUser()->getAge(), "cards" => array(array("reference" => $card->getDiscountCard()->getReference(), "number" => $card->getDiscountCardNumber())), "cui" => null));

        /**
         * Get All Train available from DepartureDate to DepartureDate
         * Create Payload with all information
         * Send request, catch error and store in DB for User and in Logger for Admin
         */
        $payload = array("search" => array("departure_date" => $trip->getFromDepartureDate()->format('Y-m-d\TH:i:sP'),"return_date" => null,"passengers" => $passenger,"systems" => array("sncf","db","busbud","idtgv","ouigo","trenitalia","ntv","hkx","renfe","benerail","ocebo","westbahn","locomore","flixbus","timetable"),"exchangeable_part" => null,"departure_station_id" => $trip->getDepartureStationId()->getId(),"via_station_id"=>null,"arrival_station_id" => $trip->getArrivalStationId()->getId(),"exchangeable_pnr_id"=>null));
        $client = new GuzzleHttp\Client();
        try {
            $response = $client->post($this::API_URL.'/search', [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                    'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                ],
                'json' => $payload
            ]);
        }
        catch (\Exception $e) {
            $this->LogSystem("alert", '[CRON] :: Error during search train available', $e->getMessage(), $this::API_URL.'/search', $payload);

            $order->setError(true);
            $this->em->persist($order);
            $this->em->flush();
            return;
        }

        /**
         * Decode response from guzzle
         * If there are no train available -> exit
         */
        $trainAvailable = json_decode($response->getBody()->getContents(), true);
        if (!$trainAvailable['trips']) {
            exit();
        }

        /**
         * For each train available remove all tgvmax with no places
         * and remove all train out of datetime range
         */
        $trainCompatibleWithTheSearch = array();
        foreach ($trainAvailable['trips'] as $train) {
            if (isset($train['long_unsellable_reason']) AND $train['long_unsellable_reason'] === "Il n’y a plus de places TGVmax disponibles sur ce trajet.") {
                continue;
            }
            if (!($train['departure_date'] >= $trip->getFromDepartureDate()->format('Y-m-d\TH:i:sP') AND $train['departure_date'] <= $trip->getToDepartureDate()->format('Y-m-d\TH:i:sP'))) {
                continue;
            }
            $trainCompatibleWithTheSearch[] = $train;
        }

        /**
         * For last trains, i compile trip and folder array in only one.
         * If lastTrainCompatible is null -> exit
         */
        $lastTrainCompatible = array();
        foreach ($trainCompatibleWithTheSearch as $train) {
            foreach ($trainAvailable['folders'] as $folder) {
                if ($train['folder_id'] == $folder['id']) {
                    $lastTrainCompatible[] = array("folder" => $folder, "trip" => $train);
                }
            }
        }
        if (!$lastTrainCompatible) {
            exit();
        }

        /**
         * For last trains compatible, check if haven't already train for this trip
         * And if train already booked for this trip return
         */
        foreach ($lastTrainCompatible as $train) {
            $checkTrip = $this->em->getRepository(Trip::class)->findOneById($trip->getId());
            if($checkTrip->getIsReserved() == false AND $checkTrip->getBooking() == true) {
                $order->setDepartureDate(new \DateTime($train['folder']['departure_date']));
                $order->setArrivalDate(new \DateTime($train['folder']['arrival_date']));
                $order->setSearchId($train['folder']['search_id']);
                $order->setFolderId($train['folder']['id']);
                $order->setDirection($train['folder']['direction']);
                $this->em->persist($order);

                /**
                 * Search information on the train
                 */
                try {
                    $response = $client->get($this::API_URL.'/search/'.$train['folder']['search_id'].'/folders/'.$train['folder']['id'].'?direction='.$train['folder']['direction'], [
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                            'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                        ]
                    ]);
                }
                catch (\Exception $e) {
                    $this->LogSystem("alert", '[CRON] :: Error during search the information of train', $e->getMessage(), $this::API_URL.'/search/'.$train['folder']['search_id'].'/folders/'.$train['folder']['id'].'?direction='.$train['folder']['direction'], $payload);

                    $order->setError(true);
                    $this->em->persist($order);
                    $this->em->flush();
                    return;
                }

                /**
                 * Decode response from guzzle
                 * If train have no places -> continue foreach
                 */
                $train = json_decode($response->getBody()->getContents(), true);
                if (isset($train['long_unsellable_reason']) AND $train['long_unsellable_reason'] === "Il n’y a plus de places TGVmax disponibles sur ce trajet.") {
                    continue;
                }

                /**
                 * Booking the train
                 * Create the payload
                 * Send request, catch error and store in DB for User and in Logger for Admin
                 */
                $payload = array("book" => array("search_id" => $train['folder']['search_id'],"outward_folder_id" => $train['folder']['id'],"options" => array("d81a54eeb66b11e780f16f5e2f5eaac7" => array("comfort_class" => "sncf.regular","seat" => "no_preference")),"passengers" => [array("id" => $train['trips'][0]['passenger_id'],"gender" => ($trip->getUser()->getGender() == "M.") ? "male" : "female","first_name" => $trip->getUser()->getName(),"last_name" => $trip->getUser()->getSurname(),"birthdate" => $trip->getUser()->getBirthdate()->format('Y-m-d\TH:i:sP'),"address_id" => null,"user_id" => null,"original_id" => null)],"user" => array("first_name" => $trip->getUser()->getName(),"last_name" => $trip->getUser()->getSurname(),"profile_picture_url" => null,"email" => $trip->getUser()->getEmail(),"password" => null,"current_password" => null,"is_confirmed" => false,"wants_cart_expiration_reminder" => false,"wants_newsletter" => false,"wants_ics" => false,"wants_passbook" => false,"wants_proof_of_travel" => false,"ask_for_ics_and_passbook_preferences" => false,"needs_verification" => false,"locale" => null,"preferred_currency" => null,"calendar_public_url" => null,"source" => null,"correlation_key" => null,"godparent_token" => null,"signup_referral" => null,"third_party_id" => null,"flexibility_filter" => null,"comfort_filter" => null,"concur_auth_code" => null,"is_concur" => false,"pro_token" => null,"gender" => "male","birthdate" => null,"auth_token" => null,"suggested_station_ids" => array(),"identification_document_ids" => array(),"address_id" => null,"organization_id" => null,"preferred_supervisor_id" => null,"supervisor_ids" => array())));
                try {
                    $response = $client->post($this::API_URL.'/book', [
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                            'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                        ],
                        'json' => $payload
                    ]);
                }
                catch (\Exception $e) {
                    $this->LogSystem("alert", '[CRON] :: Error during create option on the train', $e->getMessage(), $this::API_URL.'/book', $payload);

                    $order->setError(true);
                    $this->em->persist($order);
                    $this->em->flush();
                    return;
                }

                /**
                 * Decode response from guzzle
                 * If no response -> continue foreach
                 * If response -> make payments
                 */
                $resultBookingTrain = json_decode($response->getBody()->getContents(), true);
                if($resultBookingTrain) {
                    $order->setOrderId($resultBookingTrain['book']['order_id']);
                    $order->setPnrId($resultBookingTrain['book']['pnr_ids'][0]);
                    $this->em->persist($order);

                    /**
                     * Booking Payment
                     * Create the payload
                     * Send request, catch error and store in DB for User and in Logger for Admin
                     */
                    $payload = array("payment" => array("mean" => "free","cents" => 0,"currency" => "EUR","holder" => null,"number" => null,"expiration_month" => null,"expiration_year" => null,"cvv_code" => null,"status" => null,"verification_form" => null,"verification_url" => null,"can_save_payment_card" => false,"is_new_customer" => false,"digitink_value" => null,"pnr_ids" => [$resultBookingTrain['book']['pnr_ids'][0]],"after_sales_charge_ids" => array(),"subscription_ids" => array(),"exchange_ids" => array(),"coupon_ids" => array(),"order_id"  => $resultBookingTrain['book']['order_id'],"payment_card_id" => null));
                    try {
                        $response = $client->post($this::API_URL.'/payments', [
                            'headers' => [
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                                'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                            ],
                            'json' => $payload
                        ]);
                    }
                    catch (\Exception $e) {
                        $this->LogSystem("alert", '[CRON] :: Error during booking payment', $e->getMessage(), $this::API_URL.'/payments', $payload);

                        /**
                         * Remove option for next cron
                         */
                        $this->deleteTemporaryBooking($resultBookingTrain['book']['order_id']);

                        $order->setError(true);
                        $this->em->persist($order);
                        $this->em->flush();
                        return;
                    }

                    /**
                     * Decode response from guzzle
                     * If no response -> continue foreach
                     * If response -> confirm booking
                     */
                    $resultBookingPayment = json_decode($response->getBody()->getContents(), true);
                    if ($resultBookingPayment) {
                        $order->setPaymentId($resultBookingPayment['payment']['id']);
                        $this->em->persist($order);

                        /**
                         * Booking Confirmation
                         * Create the payload
                         * Send request, catch error and store in DB for User and in Logger for Admin
                         */
                        $payload = array("payment" => array("mean" => "free","cents" => 0,"currency" => "EUR","holder" => null,"number" => null,"expiration_month" => null,"expiration_year" => null,"cvv_code" => null,"status" => "waiting_for_confirmation","verification_form" => null,"verification_url" => null,"can_save_payment_card" => false,"is_new_customer" => false,"digitink_value" => null,"pnr_ids" => $resultBookingTrain['book']['pnr_ids'][0],"after_sales_charge_ids" => [],"subscription_ids" => [],"exchange_ids" => [],"coupon_ids" => [],"order_id" => $resultBookingTrain['book']['order_id'],"payment_card_id"  => null));
                        try {
                            $response = $client->post($this::API_URL.'/payments/'.$resultBookingPayment['payment']['id'].'/confirm', [
                                'headers' => [
                                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                                    'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                                ],
                                'json' => $payload
                            ]);
                        }
                        catch (\Exception $e) {
                            $this->LogSystem("alert", '[CRON] :: Error during booking confirmation', $e->getMessage(), $this::API_URL.'/payments/'.$resultBookingPayment['payment']['id'].'/confirm', $payload);

                            $order->setError(true);
                            $this->em->persist($order);
                            $this->em->flush();
                            return;
                        }

                        /**
                         * Booking OK, we sent mail for inform user
                         * Trainline we sent mail to user with ticket
                         * Flush data in db
                         */
                        $data = json_decode($response->getBody()->getContents(), true);

                        $trip->setIsReserved(true);
                        $this->em->persist($trip);
                        $order->setError(false);
                        $order->setPnrsRevision($data['pnrs'][0]['revision']);
                        $this->em->persist($order);
                        $this->em->flush();

                        $this->sendNotificationEmail($subject = "TGVmax Helper :: Booking OK", $trip->getUser()->getEmail(), $template = "booking", $data);

                        /**
                         * Log success and return;
                         */
                        $this->LogSystem("info", '[CRON] :: Booking success'.$trip->getId(), $trip, null, null);
                        return;
                    }
                    return;
                }
                return;
            } else {
                return;
            }
        }
    }

    /**
     * Automate Notification System
     */
    public function notificationTrain($trip) {

    }

    /**
     * Remove temporary booking
     *
     * @param $orderId
     */
    public function deleteTemporaryBooking($orderId) {

        $client = new GuzzleHttp\Client();
        $client->delete($this::API_URL.'/orders/'.$orderId, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
            ],
        ]);
        return;
    }


    /**
     * Cancel booking
     *
     * @param $tripId
     * @return bool
     */
    public function cancelBooking($tripId) {

        $order = $this->em->getRepository(OrderTrip::class)->findOneById($tripId);

        $client = new GuzzleHttp\Client();
        $payload = array("cancellation" => array("pnr_revision" => $order->getPnrsRevision(),"part" => $order->getDirection(),"cents" => null,"currency" => "EUR","penalty_cents" => null,"penalty_currency" => "EUR","order_id" => $order->getOrderId(),"pnr_id" => $order->getPnrId(),"passenger_ids" => [$order->getPassengerUuid()]));
        try {
            $response = $client->post($this::API_URL.'/orders/'.$order->getOrderId().'/cancellations', [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                    'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                ],
                'json' => $payload
            ]);
        }
        catch (\Exception $e) {
            $this->logger->alert('[CANCELLATION] :: Error during create cancellation', array(
                'date' => date('Y-m-d H:i:s'),
                'message'  => $e->getMessage(),
                'endpoint' => $this::API_URL.'/orders/'.$order->getOrderId().'/cancellations',
                'payload' => $payload
            ));
            return false;
        }
        $cancellation = json_decode($response->getBody()->getContents(), true);

        try {
            $response = $client->put($this::API_URL.'/orders/'.$order->getOrderId().'/cancellations/'.$cancellation['id'], [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
                    'x-user-agent' => 'CaptainTrain/1509467302(web) (Ember 2.12.2)'
                ],
                'json' => $payload
            ]);
        }
        catch (\Exception $e) {
            $this->LogSystem("alert", "[CANCELLATION] :: Error during confirmation cancellation", $e->getMessage(), null, null);
            return false;
        }
        $data = json_decode($response->getBody()->getContents(), true);

        // SoftDelete Order
        $this->em->remove($order);
        $this->em->flush();

        // Send Email for confirmation of deleted
        $this->sendNotificationEmail($subject = 'TGVmax Helper :: Booking OK', $order->getUser()->getEmail(), $template = "cancellation", $data);
        return true;
    }

    /**
     * Create log information
     *
     * @param $type
     * @param $subject
     * @param message
     * @param $endpoint
     * @param $payload
     */
    private function LogSystem($type, $subject, $message, $endpoint, $payload) {
        $this->logger->$type($subject, array(
            'date' => date('Y-m-d H:i:s'),
            'message'  => $message,
            'endpoint' => $endpoint,
            'payload' => $payload
        ));
    }

    /**
     * Send notification mail
     *
     * @param $subject
     * @param $email
     * @param $template
     * @param $data
     */
    private function sendNotificationEmail($subject, $email, $template, $data) {
        $message = (new \Swift_Message($subject))
            ->setFrom('noreplay@tgvmaxhelper.com')
            ->setTo($email)
            ->setBody(
                $this->templating->render(
                    'TrainBundle:Mail:'.$template.'.mail.twig',
                    array('data' => $data)
                )
            )
        ;
        $this->mailer->send($message);
    }

    /**
     * Get type of person
     *
     * @param $age
     * @return string
     */
    private function getTypeByAge($age) {
        switch ($age) {
            case ($age <= 25):
                return "youth";
                break;
            case ($age >= 26 && $age <= 59):
                return "adult";
                break;
            case ($age >= 60):
                return "senior";
                break;
        }
    }

    /**
     * Generate UUID Passenger
     */
    private function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

}