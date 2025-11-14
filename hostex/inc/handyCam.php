<?php
/**
 * devTV
 * User: DTV
 * Date: 15/11/2025
 */

namespace handyCam;


class handyCam
{

    public function  getAppDate($datestr)
    {
        $date = explode('-', $datestr);
        return $date[2].'/'.$date[1].'/'.$date[0];
    }
    public function  parseAppDate($datestr)
    {
        $date = explode('/', $datestr);
        return $date[2].'-'.$date[1].'-'.$date[0];
    }


}