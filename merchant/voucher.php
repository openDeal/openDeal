<?php

class Requester {

    /**
     * Request a resource
     *
     * @param string $url 
     * @param array $params associative array of parameters to send
     * @param string $method GET/POST
     * @param array $http_auth assoc array with 'user' and 'password' values set if needed
     * @param array $options misc assoc array of options
     * @return array of headers, body as url resource data
     */
    static public function request($url, $params = array(), $method = 'GET', $http_auth = array(), $options = array()) {
        // create cURL instance
        $ch = curl_init();

        // if empty query string, don't bother
        $query_string = '';
        if (!empty($params)) {
            if (isset($options['raw']) && $options['raw'] == true) {
                $query_string = $params;
            } else {
                if (!is_array($params) && !empty($params)) {
                    $params = array($params);
                }
                $query_string = http_build_query($params);
                if (isset($options['decode']) && $options['decode'] == 'false') {
                    $query_string = rawurldecode($query_string);
                }
            }
        }

        /* parse options */

        // timeout
        $timeout = 20; // default timeout
        if (isset($options['timeout']) && is_numeric($options['timeout'])) {
            $timeout = $options['timeout'];
        }

        // http header
        if (isset($options['header']) && !empty($options['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
        }

        /* end options parse */

        curl_setopt($ch, CURLOPT_USERAGENT, "Request/RankedByREview - (rankedbyreview.com)");
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // this is an insecure setting. =(
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // this one too
        // http auth stuff; assume user/password are keys
        if (is_array($http_auth) && isset($http_auth['user'])) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $http_auth['user'] . ':' . $http_auth['password']);
        }

        $method = strtoupper($method); // for ease of use

        switch ($method) {
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                break;
            case 'POST':

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                break;
            case 'GET':
            default:
                if (strlen($query_string) > 0) {
                    $query_string = '?' . $query_string;
                }
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_URL, $url . $query_string);
                break;
        }

        $result = curl_exec($ch);
        $data = curl_getinfo($ch);

        //error_log( print_r( $data, 1 ));
        //error_log( print_r( $result, 1));
        if (curl_errno($ch)) {
            error_log(curl_error($ch) . ": " . curl_errno($ch));
            error_log(print_r($data, 1));
            return false;
        } else {
            curl_close($ch);
        }

        // because CURLOPT_HEADER is true, we have to split up the headers and response body 
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        // process headers into a nice little array:
        $raw_headers = explode("\r\n", $header);
        $headers = self::parse_headers($header);

        // send beautiful response back
        $result = array(
            'info' => $data, // from cURL response, includes http codes
            'raw_headers' => $raw_headers,
            'headers' => $headers,
            'body' => $body,
        );



        return $result;
    }

    /**
     * Similar to pecl http's http_parse_headers
     *  but will NOT favor that if it pecl_htp extension is loaded
     *
     * @param string $headers 
     * @return array 
     */
    static public function parse_headers($headers) {
        /* extensions' handling of this is slightly different (eg: Set-Cookie) (it's actually more desirable behavior)
          if( function_exists( 'http_parse_headers' ) ) {
          return http_parse_headers( $headers );
          }
         */
        $retval = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if (isset($retval[$match[1]])) {
                    $retval[$match[1]] = array($retval[$match[1]], $match[2]);
                } else {
                    $retval[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retval;
    }

}

Class CTImporter {

    public $lastRequest = null;

    public function doGoogleQuery($what) {
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?key=AIzaSyA7WavyokokzhJj4vD1-7DRkqjjOCdsVmk";
        $url .= '&location=-33.9045518,18.4068343';
        $url .= '&radius=50000';
        $url .= '&sensor=false';
        $url .= '&keyword=' . urlencode($what);
        $res = $this->request($url);
        return $res;
    }

    public function getPlaceDetails($place) {
        $url = 'https://maps.googleapis.com/maps/api/place/details/json?reference=' . $place . "&key=AIzaSyA7WavyokokzhJj4vD1-7DRkqjjOCdsVmk";
        $url .= '&sensor=false';
        return $this->request($url);
    }

    public function request($url, $params = array(), $method = 'GET', $http_auth = array(), $options = array()) {
        // check url for relative path (assuming consumption of our own api only)
        $url_data = parse_url($url);


        if (!isset($url_data['scheme']) && !isset($url_data['host'])) {
            return false;
        }

        $data = Requester::request($url, $params, $method, $http_auth, $options);

        // store it all
        $this->lastRequest = $data;

        if ($data['info']['http_code'] === 200) {
            $body = json_decode($data['body'], 1);
            if ($body !== null) {
                return $body;
            } else {
                error_log("Error decoding data returned from {$url}");
            }
        } else {
            error_log("Error fetching {$url}");
            return false;
        }
    }

    public function initFile() {

        $file = __DIR__ . '/busdata.csv';
        $handle = fopen($file, 'w+');
        fwrite($handle, "CATEGORY,BUSINESS,ADDRESS,TELEPHONE,WEBSITE\n");
        fclose($handle);
     }



    public function writeBusiness($data) {

       // $bus = implode(",", $data);

        $file = __DIR__ . '/busdata.csv';

        $handle = fopen($file, 'a+');

       // fwrite($handle, $bus . "\n");
         fputcsv($handle, $data);

        fclose($handle);
    }

}

$categories = array(
    'Accommodation, house, motel etc.' => array(),
    'Adult products and services' => array(),
    'Alarm installation, monitoring and security services' => array(),
    'Appliances, whiteware, household electrical' => array(),
    'Art supplies' => array(),
    'Auctioneers and auctions, car, furniture etc.' => array(),
    'Auto accessories, motor trimmers, hose and fittings' => array(),
    'Baby wear, clothing and accessories' => array(),
    'Bakery, hot bread shop' => array(),
    'Batteries and tyres' => array(),
    'Beauty Therapists, beauty products, nail bars' => array(),
    'Beverages – milk, juice, coffee, etc' => array(),
    'Bicycles, clothing and accessories' => array(),
    'Blinds, curtains, window treatments' => array(),
    'Boats, skis, marine supplies' => array(),
    'Body decorations, piercings, tattoos and accessories' => array(),
    'Bookshops ' => array(),
    'Bridal and formal wear (men and women)' => array(),
    'Building, renovations' => array(),
    'Car audio' => array(),
    'Car rental companies' => array(),
    'Car wash, valet' => array(),
    'Cars and trucks, new and used' => array(),
    'Chemist, pharmacy, vitamins & medicines' => array(),
    'Childcare, , babysitting' => array(),
    'Churches, religion' => array(),
    'Cleaning services, commercial and domestic' => array(),
    'Clubs, RSL, gambling and casinos' => array(),
    'Community service and charity' => array(),
    'Computers, hardware and software' => array(),
    'Conference, wedding, sporting venues' => array(),
    'Courier and delivery services' => array(),
    'Craft and markets' => array(),
    'Delicatessen and small goods' => array(),
    'Department stores, variety shops' => array(),
    'Driving schools' => array(),
    'Dry-cleaning and laundry' => array(),
    'Education, training, schools, courses, colleges' => array(),
    'Employment and recruitment, secretarial services' => array(),
    'Entertainment, shows, concerts, sporting fixtures' => array(),
    'Equestrian supplies and saddler' => array(),
    'Fabric, sewing, wool and knitting supplies' => array(),
    'Farm supplies, feed, seed, machinery, livestock, water tanks' => array(),
    'Fast food, takeaway, home delivery' => array(),
    'Finance, banks, insurance, tax, accountants, mortgage brokers' => array(),
    'Floor coverings, carpet, rugs, tiles and timber' => array(),
    'Florists' => array(),
    'Footwear, shoe stores, shoe & bag repairs' => array(),
    'Framing, pictures, art' => array(),
    'Fruit and veg' => array(),
    'Funeral services, undertakers' => array(),
    'Furniture, bedding' => array(),
    'Garden centres, nurseries and landscape supplies' => array(),
    'Gas and fuel suppliers ' => array(),
    'Gift shops, homeware' => array(),
    'Glass and windows, home and automotive' => array(),
    'Government and councils etc.' => array(),
    'Gyms and fitness centres' => array(),
    'Hairdressers and specialist hair treatments' => array(),
    'Hardware stores, paint, wallpaper, building and plumbing supplies' => array(),
    'Health, optometrist, dentist, doctor, weight loss, masseur, chiro etc' => array(),
    'Heating, cooling, insulation, aircon' => array(),
    'Hire equipment and machinery' => array(),
    'Home entertainment, stereos, VCR, TV, DVD' => array(),
    'Hotels, pubs, discos, nightclubs' => array(),
    'Internet service providers, web designers etc.' => array(),
    'Jewellers and jewellery' => array(),
    'Kitchens, suppliers and designers' => array(),
    'Lawnmowers, trimmers and chainsaws' => array(),
    'Lawyers, barristers and solicitors' => array(),
    'Lighting suppliers, retail and wholesale' => array(),
    'Liquor stores, alcoholic beverages, retailers and distributors' => array(),
    'Mechanics, auto repairs, auto electrical' => array(),
    'Motorcycle sales, service, accessories' => array(),
    'Music stores, CDs, DVDs , musical instruments, teachers' => array(),
    'Newsagents, papers, magazines and cards' => array(),
    'Office equipment, stationery and supplies, ' => array(),
    'Opticians, contact lenses, frames, sunglasses, laser eye surgery' => array(),
    'Outdoor equipment, furniture, BBQs, camping supplies' => array(),
    'Panel beaters, spray painters, towing' => array(),
    'Party hire, fancy dress, costumes, balloons etc' => array(),
    'Pest control, fumigation' => array(),
    'Pet stores and supplies' => array(),
    'Photography, film and cameras' => array(),
    'Plumbers, electricians, tillers, roofers, refrigeration specialist etc' => array(),
    'Pools, spas, retail, accessories and supplies' => array(),
    'Printing, binding, laminating and photocopying services' => array(),
    'Real estate, sales and rentals' => array(),
    'Removalists and self storage' => array(),
    'Restaurants and cafes' => array(),
    'Retail clothing, men, women and children' => array(),
    'Retirement, rest homes' => array(),
    'Service stations (BP, Shell etc)' => array(),
    'Sheets, towels, Manchester' => array(),
    'Shopping centres, malls, megamarts' => array(),
    'Sporting goods, surf, ski, golf, fishing, etc' => array(),
    'Supermarkets, food stores, convenience stores' => array(),
    'Taxi services, limousine hire, dial-a-driver etc' => array(),
    'Telecom providers, mobiles, faxes, phones, sales, service & access' => array(),
    'Toys and games' => array(),
    'Trailers and caravans' => array(),
    'Travel agencies, tours and tourism providers' => array(),
    'Veterinary surgery and pet food supplies' => array(),
    'Video and DVD hire, cinema' => array(),
    'Waste services, bin hire, septic tank' => array(),
);

$importer = new CTImporter();
$importer->initFile();

foreach ($categories as $category => $rows) {
    $result = $importer->doGoogleQuery($category);
    if (!$result) {
        $categories[$category] = array();
    } elseif ($result['status'] == 'ZERO_RESULTS') {
        $categories[$category] = array();
    } elseif ($result['status'] == 'OVER_QUERY_LIMIT') {
        $categories[$category] = 'OVER_QUERY_LIMIT';
        /* exit; */
    } else {

        foreach ($result['results'] as $result) {
            $info = $importer->getPlaceDetails($result['reference']);
            if ($info && $info['status'] == 'OK') {
                $row = array(
                    'category' => $category,
                    'name' => $info['result']['name'],
                    'address' => $info['result']['formatted_address'],
                    'telephone' => isset($info['result']['formatted_phone_number']) ? $info['result']['formatted_phone_number'] : '',
                    
                    'website' => isset($info['result']['website']) ? $info['result']['website'] : '',
                );
                $importer->writeBusiness($row);
            
            }
        }
    }
}

