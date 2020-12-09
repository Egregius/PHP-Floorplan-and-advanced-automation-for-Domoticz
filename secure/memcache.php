<?php

	/**
	* ENGLISH:
	* Simple Stats page for Memcached server
	* Forked from (https://github.com/bainternet/Memchaced-Dashboard)
	* Turkish language support added, number display divided by thousands separator
	* Data usage values are converted to readable values from byte values
	* 
	* TÜRKÇE:
	* Memcached server için basit bir istatistik sayfası
	* Şu projeden türetilmiştir (https://github.com/bainternet/Memchaced-Dashboard)
	* Türkçe dil desteği eklenmiş, sayı gösterimleri binlik seperatör ile ayrılmış
	* Veri kullanım değerleri byte değerlerden okunabilir değerlere dönüştürülmüştür
	*
	* Simple Memchached Dashboard
	* @version 0.2.0
	* @author Sabri Ünal <yakushabb@gmail.com>
	*/

	function format_binlik($sayi)
	{
		return number_format($sayi, 0, ',', '.');
	}

	function size_formatted($size)
	{
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}


	function duration($ts)
	{

		/**
		* fonksiyon kaynağı: https://github.com/kn007/memcache.php/blob/master/memcache.php
		*/

		//global yerine biz atalayım
		$time = time();

		$years 		= (int)((($time - $ts)/(7*86400))/52.177457);
		$rem 		= (int)(($time-$ts)-($years * 52.177457 * 7 * 86400));
		$weeks 		= (int)(($rem)/(7*86400));
		$days 		= (int)(($rem)/86400) - $weeks*7;
		$hours 		= (int)(($rem)/3600) - $days*24 - $weeks*7*24;
		$mins 		= (int)(($rem)/60) - $hours*60 - $days*24*60 - $weeks*7*24*60;
		$str 		= '';
		if($years > 0)	$str .= "$years yıl, ";
		if($weeks > 0)	$str .= "$weeks hafta, ";
		if($days > 0)	$str .= "$days gün,";
		if($hours > 0)	$str .= " $hours saat ve";
		if($mins > 0)	$str .= " $mins dakika";
		return $str;
	}

	class Simple_memchached_dashboard
	{
		public $memcache = null;
		public $list	 = null;
		public $status   = null;
		public $error	= false;
		public $server   = '';
		public $port	 = '';

		function __construct($server = '127.0.0.1',$port = '11211')
		{
			$this->server = $server;
			$this->port = $port;
			$this->setup();
			$this->dashboard();
		}

		function setup()
		{
			$this->memcache = new Memcache();
			$this->memcache->addServer("$this->server:$this->port");
			$this->status = $this->memcache->getStats();
		}

		function dashboard(){
			//server info
			$this->print_server_info();
		}

		function print_server_info()
		{
			$status = $this->status;
			?>
			<a name="info"></a>
			<div class="panel panel-default top20">
				<div class="panel-heading">
					<h3 class="panel-title">Server IP: <?= $this->server ?> / Port: <?= $this->port ?></h3>
				</div>
				<div class="panel-body">
				<?php
					if ((real) $status ["cmd_get"] != 0)
					{
						$percCacheHit = ((real) $status ["get_hits"] / (real) $status ["cmd_get"] *100);
					}
					else
					{
						$percCacheHit = 0;
					}
					$percCacheHit = round($percCacheHit,3);
					$percCacheMiss = 100-$percCacheHit;

					echo "
						<table class='table'>
							<tr><td>Memcache Sunucu Sürümü </td><td> ".$status ["version"]."</td></tr>
							<tr><td>Sunucu ne kadar süredir yayında </td><td>".duration($status["time"]-$status["uptime"])."</td></tr>
							<tr><td>Yayın süresince belleklenen öğe sayısı </td><td>".format_binlik($status["total_items"])."</td></tr>
							<tr><td>Yayın süresince açılan bağlantı sayısı </td><td>".format_binlik($status["total_connections"])."</td></tr>

							<tr><td>Açık bağlantı sayısı</td><td>".$status ["curr_connections"]."</td></tr>
							<tr><td>İzin verilen en fazla bağlantı sayısı</td><td>".$status ["connection_structures"]."</td></tr>

							<tr><td>Toplam istek sayısı </td><td>".format_binlik($status["cmd_get"])."</td></tr>
							<tr><td>Toplam depolama isteği sayısı </td><td>".format_binlik($status["cmd_set"])."</td></tr>

							<tr><td>Talep edilen ve bulunan öğe sayısı </td><td>".format_binlik($status["get_hits"])." ($percCacheHit%)</td></tr>
							<tr><td>Talep edilen fakat bulunamayan öğe sayısı</td><td>".format_binlik($status["get_misses"])." ($percCacheMiss%)</td></tr>

							<tr><td>Ağdan okunan veri miktarı </td><td>".size_formatted($status["bytes_read"])."</td></tr>
							<tr><td>Ağa gönderilen veri miktarı </td><td>".size_formatted($status["bytes_written"])." </td></tr>

							<tr><td>Kullanılan bellek </td><td>".size_formatted($status['bytes'])."</td></tr>
							<tr><td>İzin verilen bellek</td><td>".size_formatted($status["limit_maxbytes"])."</td></tr>

							<tr><td>Yeni öğelere izin vermek için silinmek zorunda kalan geçerli öğe sayısı </td><td>".$status ["evictions"]."</td></tr>
					</table>";
				?>
				</div>
			</div>
			<?php
		}
	}//end class

	if (!function_exists('memcache_pconnect'))
	{
		echo '<div style="background-color: #F2DEDE; color: #B94A48; padding: 1em;">MemCache Bağlantısı kurulamadı</div>';
	}
	else
	{
		new Simple_memchached_dashboard();
	}