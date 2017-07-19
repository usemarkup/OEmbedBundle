<?php

namespace Markup\OEmbedBundle\Command;

use Markup\OEmbedBundle\Exception\OEmbedUnavailableException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
* A console command to fetch oEmbed instances and output their data.
*/
class FetchOEmbedCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oembed:fetch')
            ->setDescription('Makes a query against an oEmbed provider for data about a piece of media')
            ->addArgument('provider', InputArgument::REQUIRED, 'An oEmbed provider name')
            ->addArgument('media_id', InputArgument::REQUIRED, 'The ID of the piece of media being embedded.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $provider = $input->getArgument('provider');
        $mediaId = $input->getArgument('media_id');

        $output->writeln(sprintf('Looking up oEmbed data for media ID %s from the provider "%s".', $mediaId, $provider));

        $startTime = microtime(true);
        try {
            $oEmbed = $this->getContainer()->get('markup_oembed')->fetchOEmbed($provider, $mediaId);
        } catch (OEmbedUnavailableException $e) {
            $output->writeln(sprintf('<error>Could not fetch the oEmbed data. Reported error: %s</error>', $e->getMessage()));

            return;
        }

        $output->writeln(
            sprintf('<info>oEmbed lookup was successful!</info> (%01.3fs)', microtime(true) - $startTime)
        );
        //type first, then all the others
        $format = '%s: %s';
        $output->writeln(sprintf($format, 'type', $oEmbed->getType()));
        foreach ($oEmbed->all() as $key => $value) {
            if ($key === 'type') {
                continue;
            }
            $output->writeln(sprintf($format, $key, $value));
        }
    }
}
