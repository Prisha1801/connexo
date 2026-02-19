<?php

if (!function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function trans($key = null, $replace = [], $locale = null)
    {

        if (is_null($key)) {
            return app('translator');
        }

        $vendor_entity_name = env('VENDOR_ENTITY_NAME', 'Company');
        $vendor_entity_name_plural = env('VENDOR_ENTITY_NAME_PLURAL', 'Companies');

        $message = app('translator')->get($key, $replace, $locale);
        if (strpos($key, 'estaurant') !== false && $vendor_entity_name != 'Company' && $vendor_entity_name_plural != 'Companies' /* Also check in the value to change to is not company  */) {
            $translatedEntity_plural = __($vendor_entity_name_plural);
            $translatedEntity = __($vendor_entity_name);

            //ES
            $message = str_replace('Companyes', $translatedEntity_plural, $message);
            $message = str_replace('Companye', $translatedEntity, $message);
            $message = str_replace('companyes', strtolower($translatedEntity_plural), $message);
            $message = str_replace('companye', strtolower($translatedEntity), $message);

            //ES
            $message = str_replace('Companies', $translatedEntity_plural, $message);
            $message = str_replace('Company', $translatedEntity, $message);
            $message = str_replace('companies', strtolower($translatedEntity_plural), $message);
            $message = str_replace('company', strtolower($translatedEntity), $message);
        }

        return $message;
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . 'GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . 'MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . 'KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . 'bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . 'byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}

if( !function_exists('is_video') ){
    function is_video($path)
    {   
        try {
            if (stripos($path, base_path()) === false && stripos($path, "http://") === false && stripos($path, "https://") === false) { 
                $path = asset($path);
            }

            $stream_opts = [
                "ssl" => [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ]
            ]; 

            $headers = get_headers( $path , 1, stream_context_create($stream_opts));
            if(!$headers){
                return false;
            }

            $video_types = [
                "video/mp4",
                'video/quicktime' => 'mov'
            ];

            $file_type = "";

            if( isset( $headers['Content-Type'] ) ){
                $file_type = $headers['Content-Type'];
            }

            if( isset( $headers['content-type'] ) ){
                $file_type = $headers['content-type'];
            }

            if( in_array( $file_type, $video_types ) ){
                return true;
            } 
        } catch (\Exception $e) { return false; }

        return false;
    }
}

if (!function_exists('export_csv')) {
    function export_csv($table_name, $filename = "export")
    {
        // file name 
        $filename = $filename . '_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        // get data 
        $usersData = db_fetch("*", $table_name, false, "", "DESC", -1, 0, true);

        // file creation 
        $file = fopen('php://output', 'w');

        #$header = array("ID","Name","Email","City"); 
        #fputcsv($file, $header);
        foreach ($usersData as $key => $line) {
            fputcsv($file, $line);
        }
        fclose($file);
        exit;
    }
}

function get_image_mime_type(string $image_path): ?string
{
    $mimes  = [
        IMAGETYPE_GIF => "image/gif",
        IMAGETYPE_JPEG => "image/jpg",
        IMAGETYPE_PNG => "image/png",
        IMAGETYPE_SWF => "image/swf",
        IMAGETYPE_PSD => "image/psd",
        IMAGETYPE_BMP => "image/bmp",
        IMAGETYPE_TIFF_II => "image/tiff",
        IMAGETYPE_TIFF_MM => "image/tiff",
        IMAGETYPE_JPC => "image/jpc",
        IMAGETYPE_JP2 => "image/jp2",
        IMAGETYPE_JPX => "image/jpx",
        IMAGETYPE_JB2 => "image/jb2",
        IMAGETYPE_SWC => "image/swc",
        IMAGETYPE_IFF => "image/iff",
        IMAGETYPE_WBMP => "image/wbmp",
        IMAGETYPE_XBM => "image/xbm",
        IMAGETYPE_ICO => "image/ico"
    ];

    if (($image_type = exif_imagetype($image_path))
        && (array_key_exists($image_type, $mimes))
    ) {
        return $mimes[$image_type];
    }
    return NULL;
}

if (!function_exists('detect_file_type')) {
    function detect_file_type($ext)
    {
        if (
            $ext  == "jpg" ||
            $ext == "jpeg" ||
            $ext == "png" ||
            $ext == "gif"
        ) {
            return "image";
        } else if (
            $ext == "mp4" ||
            $ext == "mov"
        ) {
            return "video";
        } else if (
            $ext == "csv"
        ) {
            return "csv";
        } else if (
            $ext == "pdf"
        ) {
            return "pdf";
        } else if (
            $ext == "xlsx" ||
            $ext == "xls" ||
            $ext == "docx" ||
            $ext == "doc" ||
            $ext == "txt"
        ) {
            return "doc";
        } else if (
            $ext == "mp3" ||
            $ext == "ogg"
        ) {
            return "audio";
        } else {
            return "other";
        }
    }
}

if (!function_exists('detect_file_icon')) {
    function detect_file_icon($detect)
    {
        switch ($detect) {
            case 'image':
                $text = "primary";
                $icon = "fad fa-image-polaroid";
                $icon = "fad fa-image-polaroid";
                break;

            case 'video':
                $text = "danger";
                $icon = "fab fa-youtube fs-1 text-info";
                break;

            case 'audio':
                $text = "primary";
                $icon = "fas fa-music fs-1 text-info";
                break;

            case 'csv':
                $text = "info";
                $icon = "fad fa-file-csv";
                break;

            case 'pdf':
                $text = "danger";
                $icon = "fas fa-file-pdf fs-1 text-info";
                break;

            case 'doc':
                $text = "warning";
                $icon = "fad fa-file-alt";
                break;

            case 'zip':
                $text = "warning";
                $icon = "fad fa-file-archive";
                break;

            case 'folder':
                $text = "warning";
                $icon = "fad fa-file-archive";
                break;

            default:
                $text = "success";
                $icon = "fad fa-file-alt";
                break;
        }

        return [
            "text" => $text,
            "icon" => $icon
        ];
    }
}

if( !function_exists('remove_file_path') ){
    function remove_file_path( $file ){
        if( $file != "" && stripos( strtolower($file) , "https://") !== false ||  stripos( strtolower($file) , "http://") !== false ){
            $file = str_replace( url()."/uploads/file_manager/", "", $file);
        }

        return $file;
    }
}

if (!function_exists('get_data')) {
    function get_data($data, $field, $type = '', $value = '', $class = 'active'){
        if( is_array($data) ){
            if(!empty($data) && isset($data[$field]) ){
                switch ($type) {
                    case 'checkbox':
                        if($data[$field] == $value){
                            return 'checked';
                        }
                        break;

                    case 'radio':
                        if($data[$field] == $value){
                            return 'checked';
                        }
                        break;

                    case 'select':
                        if($data[$field] == $value){
                            return 'selected';
                        }
                        break;

                    case 'class':
                        if($data[$field] == $value){
                            return $class;
                        }
                        break;

                    default:
                        return $data[$field];
                        break;
                }
            }
        }else{
            if(!empty($data) && isset($data->$field) ){
                switch ($type) {
                    case 'checkbox':
                        if($data->$field == $value){
                            return 'checked';
                        }
                        break;

                    case 'radio':
                        if($data->$field == $value){
                            return 'checked';
                        }
                        break;

                    case 'select':
                        if($data->$field == $value){
                            return 'selected';
                        }
                        break;

                    case 'class':
                        if($data->$field == $value){
                            return $class;
                        }
                        break;

                    default:
                        return $data->$field;
                        break;
                }
            }
        }

        return false;
    };
}

if (!function_exists('mime2ext')) {
    function mime2ext($mime)
    {
        if (is_array($mime)) {
            $mime = end($mime);
        }

        $mime_map = [
            'video/3gpp2' => '3g2',
            'video/3gp' => '3gp',
            'video/3gpp' => '3gp',
            'application/x-compressed' => '7zip',
            'audio/x-acc' => 'aac',
            'audio/ac3' => 'ac3',
            'application/postscript' => 'ai',
            'audio/x-aiff' => 'aif',
            'audio/aiff' => 'aif',
            'audio/x-au' => 'au',
            'video/x-msvideo' => 'avi',
            'video/msvideo' => 'avi',
            'video/avi' => 'avi',
            'application/x-troff-msvideo' => 'avi',
            'application/macbinary' => 'bin',
            'application/mac-binary' => 'bin',
            'application/x-binary' => 'bin',
            'application/x-macbinary' => 'bin',
            'image/bmp' => 'bmp',
            'image/x-bmp' => 'bmp',
            'image/x-bitmap' => 'bmp',
            'image/x-xbitmap' => 'bmp',
            'image/x-win-bitmap' => 'bmp',
            'image/x-windows-bmp' => 'bmp',
            'image/ms-bmp' => 'bmp',
            'image/x-ms-bmp' => 'bmp',
            'application/bmp' => 'bmp',
            'application/x-bmp' => 'bmp',
            'application/x-win-bitmap' => 'bmp',
            'application/cdr' => 'cdr',
            'application/coreldraw' => 'cdr',
            'application/x-cdr' => 'cdr',
            'application/x-coreldraw' => 'cdr',
            'image/cdr' => 'cdr',
            'image/x-cdr' => 'cdr',
            'zz-application/zz-winassoc-cdr' => 'cdr',
            'application/mac-compactpro' => 'cpt',
            'application/pkix-crl' => 'crl',
            'application/pkcs-crl' => 'crl',
            'application/x-x509-ca-cert' => 'crt',
            'application/pkix-cert' => 'crt',
            'text/css' => 'css',
            'text/x-comma-separated-values' => 'csv',
            'text/comma-separated-values' => 'csv',
            'application/vnd.msexcel' => 'csv',
            'text/csv' => 'csv',
            'application/x-director' => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/x-dvi' => 'dvi',
            'message/rfc822' => 'eml',
            'application/x-msdownload' => 'exe',
            'video/x-f4v' => 'f4v',
            'audio/x-flac' => 'flac',
            'video/x-flv' => 'flv',
            'image/gif' => 'gif',
            'application/gpg-keys' => 'gpg',
            'application/x-gtar' => 'gtar',
            'application/x-gzip' => 'gzip',
            'application/mac-binhex40' => 'hqx',
            'application/mac-binhex' => 'hqx',
            'application/x-binhex40' => 'hqx',
            'application/x-mac-binhex40' => 'hqx',
            'text/html' => 'html',
            'image/x-icon' => 'ico',
            'image/x-ico' => 'ico',
            'image/vnd.microsoft.icon' => 'ico',
            'text/calendar' => 'ics',
            'application/java-archive' => 'jar',
            'application/x-java-application' => 'jar',
            'application/x-jar' => 'jar',
            'image/jp2' => 'jp2',
            'video/mj2' => 'jp2',
            'image/jpx' => 'jp2',
            'image/jpm' => 'jp2',
            'image/jpeg' => 'jpg',
            'image/pjpeg' => 'jpeg',
            'application/x-javascript' => 'js',
            'application/json' => 'json',
            'text/json' => 'json',
            'application/vnd.google-earth.kml+xml' => 'kml',
            'application/vnd.google-earth.kmz' => 'kmz',
            'text/x-log' => 'log',
            'audio/x-m4a' => 'm4a',
            'application/vnd.mpegurl' => 'm4u',
            'audio/midi' => 'mid',
            'application/vnd.mif' => 'mif',
            'video/quicktime' => 'mov',
            'video/x-sgi-movie' => 'movie',
            'audio/mpeg' => 'mp3',
            'audio/mpg' => 'mp3',
            'audio/mpeg3' => 'mp3',
            'audio/mp3' => 'mp3',
            'video/mp4' => 'mp4',
            'video/mpeg' => 'mpeg',
            'application/oda' => 'oda',
            'audio/ogg' => 'ogg',
            'video/ogg' => 'ogg',
            'application/ogg' => 'ogg',
            'application/x-pkcs10' => 'p10',
            'application/pkcs10' => 'p10',
            'application/x-pkcs12' => 'p12',
            'application/x-pkcs7-signature' => 'p7a',
            'application/pkcs7-mime' => 'p7c',
            'application/x-pkcs7-mime' => 'p7c',
            'application/x-pkcs7-certreqresp' => 'p7r',
            'application/pkcs7-signature' => 'p7s',
            'application/pdf' => 'pdf',
            'application/octet-stream' => 'pdf',
            'application/x-x509-user-cert' => 'pem',
            'application/x-pem-file' => 'pem',
            'application/pgp' => 'pgp',
            'application/x-httpd-php' => 'php',
            'application/php' => 'php',
            'application/x-php' => 'php',
            'text/php' => 'php',
            'text/x-php' => 'php',
            'application/x-httpd-php-source' => 'php',
            'image/png' => 'png',
            'image/x-png' => 'png',
            'application/powerpoint' => 'ppt',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.ms-office' => 'ppt',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop' => 'psd',
            'image/vnd.adobe.photoshop' => 'psd',
            'audio/x-realaudio' => 'ra',
            'audio/x-pn-realaudio' => 'ram',
            'application/x-rar' => 'rar',
            'application/rar' => 'rar',
            'application/x-rar-compressed' => 'rar',
            'audio/x-pn-realaudio-plugin' => 'rpm',
            'application/x-pkcs7' => 'rsa',
            'text/rtf' => 'rtf',
            'text/richtext' => 'rtx',
            'video/vnd.rn-realvideo' => 'rv',
            'application/x-stuffit' => 'sit',
            'application/smil' => 'smil',
            'text/srt' => 'srt',
            'image/svg+xml' => 'svg',
            'application/x-shockwave-flash' => 'swf',
            'application/x-tar' => 'tar',
            'application/x-gzip-compressed' => 'tgz',
            'image/tiff' => 'tiff',
            'text/plain' => 'txt',
            'text/x-vcard' => 'vcf',
            'application/videolan' => 'vlc',
            'text/vtt' => 'vtt',
            'audio/x-wav' => 'wav',
            'audio/wave' => 'wav',
            'audio/wav' => 'wav',
            'application/wbxml' => 'wbxml',
            'video/webm' => 'webm',
            'audio/x-ms-wma' => 'wma',
            'application/wmlc' => 'wmlc',
            'video/x-ms-wmv' => 'wmv',
            'video/x-ms-asf' => 'wmv',
            'application/xhtml+xml' => 'xhtml',
            'application/excel' => 'xl',
            'application/msexcel' => 'xls',
            'application/x-msexcel' => 'xls',
            'application/x-ms-excel' => 'xls',
            'application/x-excel' => 'xls',
            'application/x-dos_ms_excel' => 'xls',
            'application/xls' => 'xls',
            'application/x-xls' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-excel' => 'xlsx',
            'application/xml' => 'xml',
            'text/xml' => 'xml',
            'text/xsl' => 'xsl',
            'application/xspf+xml' => 'xspf',
            'application/x-compress' => 'z',
            'application/x-zip' => 'zip',
            'application/zip' => 'zip',
            'application/x-zip-compressed' => 'zip',
            'application/s-compressed' => 'zip',
            'multipart/x-zip' => 'zip',
            'text/x-scriptzsh' => 'zsh'
        ];

        return isset($mime_map[$mime]) === true ? $mime_map[$mime] : false;
    }
}

if( !function_exists('get_file_url') ){
    function get_file_url( $file ){
        if ($file !== null) {
            if( $file != "" && stripos( strtolower($file) , "https://") !== false ||  stripos( strtolower($file) , "http://") !== false ){
                return $file;
            }else{
                $file = str_replace( public_path(), "", $file);
                return asset( "".$file );
            }
        }else{
            return $file;
        }
    }
}

