<?php

namespace App\Services;

interface YelpServiceInterface
{
    /**
     * Ask our yelp api client for a location's details when provided a location model (/App/Models/Location),
     * the id for a location model (int), or a yelp_id (string)
     *
     * @param mixed (int|string|/App/Models/Location) $location
     * @return mixed
     */
    public function details($location);

    /**
     * Perform a search of a given zip code, calling the yelp API and collecting the results
     * until we reach our pagination limit for this search
     *
     * @param $zip // the zip code to center
     * @param array $options
     *      [slow] boolean // slow down the search if I'm worried about hitting a yelp API throttle
     *      [...] additional options may be passed to the searchYelp method via this array as well
     * @return array
     */
    public function search($zip, $options = []);

    /**
     * Call our Yelp API client to Search restaurants centered on a given zip
     *
     * @param $zip // The zip code to center our search on
     * @param $offset // Used for tracking current pagination
     * @param array|null $options
     *      [sort] string // Sort type
     *      [attributes] string // Any additional search attributes you'd like to include
     * @return array|null // Search results
     */
    public function searchYelp($zip, $offset = 0, $options = []);
}
