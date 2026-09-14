<?php
/* database config */ 

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$username = getenv('DB_USERNAME') ?: '';
$password = getenv('DB_PASSWORD') ?: '';
$name = getenv('DB_NAME') ?: '';
$driver = getenv('DB_DRIVER') ?: 'mysqli';

$cfg['db']['hostname'] = "$host:$port";
$cfg['db']['username'] = $username;
$cfg['db']['password'] = $password;
$cfg['db']['database'] = $name;

$cfg['db']['dbdriver'] = $driver;

/* module location HMVC */
$config['folder_modules']    = 'modules';
$config['modules_locations'] = array(
    getcwd().'/'.$config['folder_modules'].'/' => '../../'.$config['folder_modules'].'/',
);

$config['template_web']     = 'default';
$config['template_admin']   = 'atlant';

$config['mycript']          = true;
$config['encryption_key']   = getenv('ENCRYPTION_KEY') ?: '';

$config['activeLog']        = true;
$config['activeChat']       = false;

/* Authentication hardening. Values can be overridden by deployment env vars. */
$config['auth_rate_limit_attempts'] = max(1, (int) (getenv('AUTH_RATE_LIMIT_ATTEMPTS') ?: 5));
$config['auth_rate_limit_window'] = max(60, (int) (getenv('AUTH_RATE_LIMIT_WINDOW') ?: 900));
$config['auth_rate_limit_lockout'] = max(60, (int) (getenv('AUTH_RATE_LIMIT_LOCKOUT') ?: 900));
$config['auth_captcha_ttl'] = max(60, (int) (getenv('AUTH_CAPTCHA_TTL') ?: 300));

/* SMTP credentials must be provided by the deployment environment. */
$config['smtp_host'] = getenv('SMTP_HOST') ?: '';
$config['smtp_port'] = max(1, (int) (getenv('SMTP_PORT') ?: 587));
$config['smtp_username'] = getenv('SMTP_USERNAME') ?: '';
$config['smtp_password'] = getenv('SMTP_PASSWORD') ?: '';
$config['smtp_secure'] = getenv('SMTP_SECURE') ?: 'tls';
$config['smtp_from'] = getenv('SMTP_FROM') ?: $config['smtp_username'];
$config['smtp_from_name'] = getenv('SMTP_FROM_NAME') ?: 'MHC SOS';

$config['base_url']         = rtrim(getenv('APP_BASE_URL') ?: "http://localhost:3004/", '/').'/';
$config['cookie_secure']    = strtolower((string) parse_url($config['base_url'], PHP_URL_SCHEME)) === 'https';
$config['sess_regenerate_destroy'] = true;
$config['proxy_ips']        = getenv('TRUSTED_PROXY_IPS') ?: '';
//$config['base_url']         = "https://mhc.internationalsos.co.id/";
//$config['domain']  = $_SERVER['SERVER_NAME'];
//$config['domain']           = 'hondacbrcommunity.com';
$config['domain'] = 'sos.co.id';


$config['action_mask'] = array(
	"add" 		=> "Menambahkan",
	"edit"		=> "Mengupdate",
	"delete" 	=> "Menghapus"
);

$config['jenis_kelamin'] = array(
        'M' => "Laki-Laki",
        'F' => "Perempuan"
    );

$config['status_data'] = array(
        1 => "Aktif",
        0 => "Non Aktif"
    );

$config['target_url'] = array(
        '_blank' => "_Blank",
        'parent' => "Parent"
    );

$config['banner_position'] = array(
        'slider' => "Slider",
        'top' => "Top",
        'sidebar' => "Sidebar"
    );

$config['status_tampil'] = array(
        1 => "Ditampilkan",
        0 => "Tidak Ditampilkan"
    );

$config['site_type'] = array(
        2 => "Community",
        1 => "Utama"
    );
$config['template'] = array(
       // 'default'   => "Default",
        'lite'      => "Template Lite Kommunity"
    );

$config['type_static_content']  = array(
        'tentang-kami'          => 'Tentang Kami',
        'sejarah'               => 'Sejarah',
        'hubungi-kami'          => 'Hubungi Kami',
        'advertising'           => 'Adverstising',
        'disclaimer'            => 'Disclaimer',
        'privacy-policy'        => 'Privacy Policy',
        'term-of-use'           => 'Term Of Use'
    );

$config['sosmed'] = array(
        1 => array(
                "key"  => "fb",
                "name" => "facebook",
                "image"=> "facebook.png"
            ),
        2 => array(
                "key"  => "tw",
                "name" => "twitter",
                "image"=> "twitter.png"
            ),
        3 => array(
                "key"  => "ig",
                "name" => "instagram",
                "image"=> "instagram.png"
            )
    );

// upload path...
$config['upload_path']              = getcwd()."/assets/collections/";
$config['upload_path_klub']         = $config['upload_path']."/klub";
$config['upload_path_video']        = $config['upload_path']."/video";
$config['upload_path_user']         = $config['upload_path']."/user";
$config['upload_path_template']     = $config['upload_path']."/template";
$config['upload_path_content']      = $config['upload_path']."/content";
$config['upload_path_banner']       = $config['upload_path']."/banner";
$config['upload_path_photo']        = $config['upload_path']."/photo";
$config['upload_path_event']        = $config['upload_path']."/event";
$config['upload_path_kopdar']       = $config['upload_path']."/kopdar";
$config['upload_path_anggota_klub'] = $config['upload_path']."/anggota_klub";
$config['upload_path_member']       = $config['upload_path']."/member";
$config['upload_path_rank']         = $config['upload_path']."/rank";
$config['upload_path_badge']        = $config['upload_path']."/badge";
$config['upload_path_file']         = $config['upload_path']."/file";
