<?php
namespace App\Console\Commands;

use Spatie\Sitemap\Tags\Url;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $urlSite = config('app.url');

        SitemapGenerator::create($urlSite)
        ->hasCrawled(function(Url $url) {
            if( preg_match('/(page=)/', $url->url) ) {
                return false;
            }

            return $url;
        })
        ->writeToFile(base_path('../public_html/sitemap.xml'));

        $curl = curl_init("http://www.google.com/ping?sitemap={$urlSite}/sitemap.xml");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        echo "Ping a google sitemap: {$httpCode}";
    }
}