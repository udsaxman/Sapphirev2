<?php

class eveAPI
{
    public $_id = 0;
    public $_key = "";
    public $_root = "https://api.eve-online.com";


    function Init($inID, $inKey)
    {
        $this->_id = $inID;
        $this->_key = $inKey;
    }

    /*
     * Base request function. Queries the given target, passing authentication data as well as any args listed in $paramarray to
     * the specified target hosted on the set API root.
     * @param string $target API target. For example, "/account/Characters.xml.aspx". Requires prefix /.
     * @param mixed $paramarray Parameters to pass with the request. Note that authentication is automatically sent.
     * @return mixed|bool Returns a SimpleXML object derived from the returned XML or false if an error was generated.
     */
    function apiRequest($target, $paramarray)
    {
        $ch = curl_init(CURLOPT_HTTPAUTH);
        $paramarray['keyID'] = $this->_id;
        $paramarray['vCode'] = $this->_key;
        $t = "?";
        foreach ($paramarray as $k => $v) {
            $t .= $k . "=" . $v . "&";
        }
        $t = substr($t, 0, -1);

        $xml = simplexml_load_file($this->_root . $target . $t);

        return $xml;
    }

    function getTowerListXML()
    {
        return $this->apiRequest("/corp/StarbaseList.xml.aspx", array());
    }

    function getTowerDetailXML($id)
    {
        $paramArray = array();
        $paramArray['itemID'] = $id;
        return $this->apiRequest("/corp/StarbaseDetail.xml.aspx", $paramArray);
    }

    function getCharXML()
    {
        return $this->apiRequest("/account/Characters.xml.aspx", array());
    }

    function getValueArrayFromXML($xml, $column)
    {
        $resultArray = array();
        $count = 0;

        $xml = (array)$xml;

        $result = (array)$xml["result"];

        $rowset = (array)$result["rowset"];

        if (gettype($rowset["row"]) != "array") {
            $row = (array)$rowset["row"];

            $attributes = (array)$row["@attributes"];

            foreach ($attributes as $name => $value) {
                if ($name == $column) {
                    $resultArray[$count] = $value;
                    $count++;
                }
            }
        } else {
            $row = $rowset["row"];
            foreach ($row as $tower) {
                $superTower = (array)$tower;
                $attributes = $superTower["@attributes"];

                foreach ($attributes as $name => $value) {
                    if ($name == $column) {
                        $resultArray[$count] = $value;
                        $count++;
                    }
                }
            }
        }

        return $resultArray;
    }
}

?>