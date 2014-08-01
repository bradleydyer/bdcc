<?php

namespace Bdcc\Data\Types;

/**
 * Bdcc\Data\Types\Country
 *
 * @author David Kursten <david.kursten@bradleydyer.com>
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @author Anton McCook <anton.mccook@bradleydyer.com>
 */
class County {
    /**
     * @var array       list of counties
     */
    private static $counties    = array(
        'Southeast England'     => array(
            'Aberdeenshire',
            'Berkshire',
            'Buckinghamshire',
            'East Sussex',
            'Greater London',
            'Hampshire',
            'Hertfordshire',
            'Isle Of Wight',
            'Kent',
            'Middlesex',
            'Oxfordshire',
            'Surrey',
            'West Sussex',
        ),
        'Southwest England'     => array(
            'Avon',
            'Channel Isles',
            'Cornwall',
            'Devon',
            'Dorset',
            'Isles Of Scilly',
            'Somerset',
            'Wiltshire',
        ),
        'Central England'       => array(
            'Bedfordshire',
            'Derbyshire',
            'Gloucestershire',
            'Herefordshire',
            'Leicestershire',
            'Northamptonshire',
            'Nottinghamshire',
            'Shropshire',
            'Staffordshire',
            'Warwickshire',
            'West Midlands',
            'Worcestershire',
        ),
        'East England'          => array(
            'Cambridgeshire',
            'Lincolnshire',
            'Norfolk',
            'South Humberside',
            'Suffolk',
        ),
        'Northeast England'     => array(
            'Cleveland',
            'County Durham',
            'North Humberside',
            'North Yorkshire',
            'Northumberland',
            'South Yorkshire',
            'Tyne and Wear',
            'West Yorkshire',
        ),
        'Northwest England'     => array(
            'Cheshire',
            'Cumbria',
            'Isle Of Man',
            'Lancashire',
            'Merseyside',
        ),
        'South Wales'           => array(
            'Dyfed',
            'Gwent',
            'Mid Glamorgan',
            'Powys',
            'South Glamorgan',
            'West Glamorgan',
        ),
        'North Wales'           => array(
            'Clwyd',
            'Gwynedd',
        ),
        'Southern Scotland'     => array(
            'Ayrshire',
            'Berwickshire',
            'Clackmannanshire',
            'Dumfriesshire',
            'Dunbartonshire',
            'East Lothian',
            'Fife',
            'Isle Of Arran',
            'Kirkcudbrightshire',
            'Lanarkshire',
            'Midlothian',
            'Peeblesshire',
            'Renfrewshire',
            'Roxburghshire',
            'Selkirkshire',
            'West Lothian',
            'Wigtownshire',
        ),
        'Northern Scotland'     => array(
            'Aberdeenshire',
            'Angus',
            'Argyll',
            'Banffshire',
            'Caithness',
            'Inverness-Shire',
            'Isle Of Barra',
            'Isle Of Benbecula',
            'Isle Of Bute',
            'Isle Of Canna',
            'Isle Of Coll',
            'Isle Of Colonsay',
            'Isle Of Cumbrae',
            'Isle Of Eigg',
            'Isle Of Gigha',
            'Isle Of Harris',
            'Isle Of Iona',
            'Isle Of Islay',
            'Isle Of Jura',
            'Isle Of Lewis',
            'Isle Of Mull',
            'Isle Of North Uist',
            'Isle Of Rhum',
            'Isle Of Scalpay',
            'Isle Of Skye',
            'Isle Of South Uist',
            'Isle Of Tiree',
            'Kincardineshire',
            'Kinross-Shire',
            'Morayshire',
            'Nairnshire',
            'Orkney',
            'Perthshire',
            'Ross-Shire',
            'Shetland Islands',
            'Stirlingshire',
            'Sutherland',
        ),
        'Northern Ireland'      => array(
            'County Antrim',
            'County Armagh',
            'County Down',
            'County Fermanagh',
            'County Londonderry',
            'County Tyrone',
        ),
    );

    /**
     * Get list of all counties
     *
     * @param   boolean     $assocKeys      Whether the keys for the counties are associative
     * @return  array                       Array of counties | Associative array of counties
     * Alias of getCounties()
     */
    public static function getAllCounties($assocKeys = false)
    {
        // Save some space for array
        $list = array();

        // Iterate through the list of countries and get list of counties
        foreach (self::$counties as $country => $counties) {

            // Iterate through list of counties and push each county to the list
            foreach ($counties as $county) {
                // If associative is true set the key as the county name
                if($assocKeys) {
                    $list[$county] = $county;
                } else {
                    $list[] = $county;
                }
            }
        }

        return $list;
    }

    /**
     * Gets list of counties
     *
     * @param   boolean     $keepCountries  Whether to return associative array of country => list of counties
     * @param   boolean     $assocKeys      Whether the keys for the counties are associative
     * @return  array                       Array of counties | Associative array of countries and counties
     */
    public static function getCounties($keepCountries = false, $assocKeys = false)
    {
        if (!$keepCountries) {
            return self::getAllCounties($assocKeys);
        }

        // If associative keys is true rebuild the counties
        // array with the correct keys
        if($assocKeys) {
            $counties = array();

            foreach (self::$counties as $area => $areaCounties) {
                $counties[$area] = array();

                foreach ($areaCounties as $county) {
                    $counties[$area][$county] = $county;
                }
            }

            return $counties;
        }

        return self::$counties;
    }
}
