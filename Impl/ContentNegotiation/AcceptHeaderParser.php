<?php
namespace Plum\Rest\Server\Impl\ContentNegotiation;

class AcceptHeaderParser
{
    /**
     * Parses given Accept header into an array-list of media types sorted
     * from highest to lowest qualities
     *
     * @param string $accept
     *
     * @return array
     */
    public function parse($accept)
    {
        $types = explode(",", $accept);
        $types = array_map("trim", $types);
        $qualities = array_map(function($mt) {
            $q = (float)substr($mt, strpos($mt, ";q=") + 3);
            $q = $q ?: 1.0;

            return $q * 100;
        }, $types);

        $types = array_map(function($mt) {
            return explode(";q=", $mt)[0];
        }, $types);

        $mediaTypes = [];
        foreach($types as $i => $type) {
            $mt = [
                "q" => $qualities[$i],
                "mediaType" => $type,
            ];

            list($mt["type"], $mt["subtype"]) = explode("/", $type);

            $mediaTypes[] = $mt;
        }

        usort($mediaTypes, function($mt1, $mt2) {
            if ($mt1["q"] !== $mt2["q"])
                return $mt2["q"] - $mt1["q"];

            if ($mt1["type"] === $mt2["type"])
                if ($mt1["subtype"] === "*")
                    return 1;
                else
                    return -1;

            return 0;
        });

        return array_map(function($mt) {
            return $mt["mediaType"];
        }, $mediaTypes);
    }
} 
