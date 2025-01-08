<?php declare(strict_types=1);

namespace Classes;

class Sitemap
{
    const DEFAULT_PRIORITY = '0.8';
    const DEFAULT_FREQUENCY = 'daily';

    private string $serverUrl;
    private string $lastmod;
    private string $changefreq;
    private string $priority;

    private static string $lastUpdateFile = ROOT_PATH . '/sitemap.last';

    private static $excludedUrls = [
        '/main',
    ];

    public function __construct(
        string $changefreq = self::DEFAULT_FREQUENCY,
        string $priority = self::DEFAULT_PRIORITY,
    )
    {
        $this->priority = $priority;
        $this->changefreq = $changefreq;
        $this->serverUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $this->lastmod = date('Y-m-d') . 'T' . date('H:i:s') . '+00:00';
    }

    /**
     * @return string
     */
    public function getServerUrl(): string
    {
        return $this->serverUrl;
    }

    public function parse(mixed $urlToParse): string
    {
        $xml = '';

        if (\is_array($urlToParse)) {
            foreach ($urlToParse as $url) {
                if (\in_array($url, self::$excludedUrls)) {
                    continue;
                }
                $xml .= $this->wrapUrl($url);
            }

            return $xml;
        }

        if (\in_array($urlToParse, self::$excludedUrls)) {
            return $xml;
        }

        if (\is_string($urlToParse)) {
            return $this->wrapUrl($urlToParse);
        }

        return $xml;
    }

    public static function xmlHeader(): string
    {
        $xmltext = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xmltext .= '<urlset' . PHP_EOL;
        $xmltext .= '      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $xmltext .= '      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
        $xmltext .= '      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . PHP_EOL;
        $xmltext .= '            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

        return $xmltext;
    }

    public static function xmlFooter(): string
    {
        $xmltext = '';
        $xmltext .= '</urlset>' . PHP_EOL;

        return $xmltext;
    }

    public static function isTimeToUpdate(bool $force = false): bool
    {
        if (false === \file_exists(self::$lastUpdateFile)) {
            self::updateLastCheckTime();

            return true;
        }

        if (true === $force) {
            return true;
        }

        $lastUpdateString = \file_get_contents(self::$lastUpdateFile);
        $lastUpdate = (new \DateTime($lastUpdateString))->setTime(0, 0);
        $today = (new \DateTime())->setTime(0, 0);

        return $lastUpdate < $today;
    }

    public static function updateLastCheckTime(): void
    {
        \file_put_contents(self::$lastUpdateFile, (new \DateTime())->format('Y-m-d'));
    }

    protected function wrapUrl(
        string $url = '',
    ): string
    {
        if (false === \str_contains($url, $this->serverUrl)) {
            $url = $this->serverUrl . $url;
        }

        $result = '';
        $result .= '<url>' . PHP_EOL;
        $result .= '	<loc>' . $url . '</loc>' . PHP_EOL;
        $result .= '	<lastmod>' . $this->lastmod . '</lastmod>' . PHP_EOL;
        $result .= '	<changefreq>' . $this->changefreq . '</changefreq>' . PHP_EOL;
        $result .= '	<priority>' . $this->priority . '</priority>' . PHP_EOL;
        $result .= '</url>' . PHP_EOL;

        return $result;
    }

}
