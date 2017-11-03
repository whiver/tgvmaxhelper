<?php
namespace TrainBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TrainBundle\Entity\Trip;
use TrainBundle\Service\TrainlineManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Psr\Log\LoggerInterface;

class SearchAndBookTripCommand  extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('train:searchandbooktrip')
            ->setDescription('Search and Book Trip.')
            ->setHelp('This command is searching all trip of users and booking or notify user if trip is found...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');
        $trainline = $this->getContainer()->get('train.trainline_manager');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $logger->info('[CRON] :: Search and book trip started', array(
            'date' => date('Y-m-d H:i:s')
        ));

        $date = new \DateTime('NOW');
        $trips = $em->createQuery('SELECT t FROM TrainBundle:Trip t WHERE t.fromDepartureDate > :date AND t.fromDepartureDate < :date30 AND t.isReserved = false ORDER BY t.fromDepartureDate')
            ->setParameter('date', $date->format(\DateTime::W3C))
            ->setParameter('date30', $date->add(new \DateInterval('P30D'))->format(\DateTime::W3C))
            ->getResult();

        if($trips) {
            foreach ($trips as $trip) {
                //if ($trip->getBooking()) {
                    $trainline->bookingTrain($trip);
                //} else if ($trip->getNotification()) {
                //    $trainline->notificationTrain($trip);
                //}
                //continue;
            }
        } else {
            $logger->info('[CRON] :: No trip.', array(
                'date' => date('Y-m-d H:i:s')
            ));
        }

        $logger->info('[CRON] :: Search and book trip finished', array(
            'date' => date('Y-m-d H:i:s')
        ));
    }
}