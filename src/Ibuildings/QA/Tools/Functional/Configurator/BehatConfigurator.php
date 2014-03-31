<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Functional\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\AbstractWritableConfigurator;
use Ibuildings\QA\Tools\Common\Settings;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Can configure settings for Behat.
 *
 * Class BehatConfigurator
 *
 * @package Ibuildings\QA\Tools\Functional\Configurator
 */
class BehatConfigurator extends AbstractWritableConfigurator
{
    const ENV_DEV = 'Dev';

    const ENV_CI = 'Ci';

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var DialogHelper
     */
    protected $dialog;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param OutputInterface   $output
     * @param DialogHelper      $dialog
     * @param Settings          $settings
     * @param \Twig_Environment $twig
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        Settings $settings,
        \Twig_Environment $twig
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->settings = $settings;
        $this->twig = $twig;

        $this->settings['enableBehat'] = false;
        $this->settings['featuresDir'] = null;
    }

    /**
     * Asks user if they want to configure Behat.
     */
    public function configure()
    {
        $default = (empty($this->settings['enableBehat'])) ? true : $this->settings['enableBehat'];
        $this->settings['enableBehat'] = $this->dialog->askConfirmation(
            $this->output,
            "\nDo you want to install the Behat framework?",
            $default
        );

        if ($this->settings['enableBehat']) {
            $this->settings['featuresDir'] = $this->settings->getBaseDir() . '/features';
        }
    }

    /**
     * Installs required config files and examples
     */
    public function writeConfig()
    {
        if ($this->shouldWrite() === false) {
            return;
        }
        $this->askAdditionalQuestions($this->output);

        $this->writeBehatYamlFiles($this->output);
        $this->writeBehatExamples($this->output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function askAdditionalQuestions(OutputInterface $output)
    {
        $this->askBaseProduction($output);
        $this->askBaseDifferentEnvironment($output, self::ENV_CI);
        $this->askBaseDifferentEnvironment($output, self::ENV_DEV);
    }

    /**
     * Ask base url for project
     *
     * @param OutputInterface $output
     */
    protected function askBaseProduction(OutputInterface $output)
    {
        $default = (empty($this->settings['baseUrl'])) ? 'http://www.ibuildings.nl' : $this->settings['baseUrl'];
        $this->settings['baseUrl'] = $this->dialog->askAndValidate(
            $output,
            "What is base url of your application? [{$default}] ",
            function ($data) {
                if (substr($data, 0, 4) == 'http') {
                    return $data;
                }
                throw new \Exception("Url needs to start with http");
            },
            false,
            $default
        );
    }

    /**
     * Ask Base url for an different environment environment
     *
     * @param OutputInterface $output
     * @param string          $environment
     */
    protected function askBaseDifferentEnvironment(OutputInterface $output, $environment)
    {
        $baseUrlCi = (empty($this->settings['baseUrl' . $environment]))
            ? $this->suggestDomain($this->settings['baseUrl'], strtolower($environment))
            : $this->settings['baseUrlCi'];

        $this->settings['baseUrl' . $environment] = $this->dialog->askAndValidate(
            $output,
            "What is base url of the " . strtolower($environment) . " environment? [$baseUrlCi] ",
            function ($data) {
                if (substr($data, 0, 4) == 'http') {
                    return $data;
                }
                throw new \Exception("Url needs to start with http");
            },
            false,
            $baseUrlCi
        );
    }

    /**
     * Install Behat yaml files.
     *
     * @codeCoverageIgnore
     */
    protected function writeBehatYamlFiles()
    {
        // copy behat.yml
        $fh = fopen($this->settings->getBaseDir() . '/behat.yml', 'w');
        fwrite(
            $fh,
            $this->getConfigContent('behat.yml.dist', $this->settings->getArrayCopy())
        );
        fclose($fh);

        // copy behat.yml
        $fh = fopen($this->settings->getBaseDir() . '/behat.dev.yml', 'w');
        fwrite(
            $fh,
            $this->getConfigContent('behat.dev.yml.dist', $this->settings->getArrayCopy())
        );
        fclose($fh);
    }

    /**
     * Suggest a new domain based on the 'main url' and a subdomain string.
     *
     * @param string $url  the main domain
     * @param string $part the subdomain string
     *
     * @return string
     */
    protected function suggestDomain($url, $part)
    {
        $urlParts = parse_url($url);

        $scheme = $urlParts['scheme'];
        $host = $urlParts['host'];

        if (strrpos($host, 'www') !== false) {
            return $scheme . '://' . str_replace('www', $part, $host);
        }

        $hostParts = explode('.', $host);
        if (count($hostParts) > 2) {
            // change first part of the hostname
            $hostParts[0] = $part;

            return $scheme . '://' . implode('.', $hostParts);
        } else {
            // prefix hostname
            return $scheme . '://' . $part . '.' . implode('.', $hostParts);
        }
    }

    /**
     * Install a Behat feature example.
     *
     * @param OutputInterface $output
     *
     * @codeCoverageIgnore
     */
    protected function writeBehatExamples(OutputInterface $output)
    {

        if (is_dir($this->settings['featuresDir'])) {
            $output->writeln("<error>Features directory already present. No example features are installed.</error>");

            return;
        }

        try {
            $filesystem = new Filesystem();
            $filesystem->mirror(
                $this->settings->getPackageBaseDir() . '/config-dist/features',
                $this->settings['featuresDir']
            );
        } catch (\Exception $e) {
            $output->writeln(
                "<error>Something went wrong when creating the features directory" . $e->getMessage() . "</error>"
            );

            return;
        }
    }

    /**
     * @inheritdoc
     */
    protected function shouldWrite()
    {
        return $this->settings['enableBehat'] === true;
    }
}
