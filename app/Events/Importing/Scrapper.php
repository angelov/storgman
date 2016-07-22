<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of EESTEC Platform.
 *
 * EESTEC Platform is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EESTEC Platform is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EESTEC Platform.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EESTEC Platform
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Events\Importing;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class Scrapper
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $url
     * @return array
     */
    public function parseEvents($url)
    {
        $crawler = $this->client->request('GET', $url);

        $events = [];

        $crawler->filter('script')->each(function (Crawler $node) use (&$events, $url) {
            $text = $node->text();

            $containers = [
                "#events-open-for-applicationcontainer",
                "#events-in-progresscontainer",
                "#past-eventscontainer"
            ];

            foreach ($containers as $container) {

                if (strpos($text, $container)) {

                    $items = explode("'items':", $text);
                    $items = $items[1];
                    $items = trim($items);

                    $items = preg_replace('/\s+/', ' ', $items);
                    $items = str_replace("'", '"', $items);
                    $items = str_replace("], }", "] }", $items);
                    $items = str_replace("}, ]", "} ]", $items);
                    $items = str_replace("\", ]", "\" ]", $items);
                    $items = str_replace("\", }", "\" }", $items);

                    $length = strlen($items);

                    $items = substr($items, 0, $length-6);
                    $items = json_decode($items, true);

                    foreach ($items as $item) {

                        $event = [];

                        $description =  $item["description"];
                        $description = str_replace("\n", "", $description);
                        $description = str_replace("\r", "", $description);

                        $event['title'] = $item['title'];
                        $event['organizer'] = trim(explode("by", explode("<hr>", $description)[0])[1]);
                        $event['start'] = explode("</date>", explode("<date>", $description)[1])[0];
                        $event['end'] =  explode("</date>", explode("till <date>", $description)[1])[0];
                        $event['deadline'] = trim(explode("</date>", explode("Application Deadline <date>", $description)[1])[0]);
                        $event['image'] = $item['large'][0];
                        $event['url'] = $item['button_list'][0]['url'];
                        $parts = explode("<hr>", $description);
                        $event['details'] = $parts[count($parts) - 1];

                        $events[] = $event;

                    }

                }

            }

        });

        return $events;
    }
}
