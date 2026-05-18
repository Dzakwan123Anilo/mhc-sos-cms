<?php
js_hight_chart();
$jenkel = cfg('jenis_kelamin');
?>

<div class="row">

	<div class="col-md-12">
        <div class="panel" style="padding:10px;">
            <blink>
            <b style="color:red; font-size:15px;">JIKA AKAN MENULIS ARTIKEL TENTANG HBD2015 SILAHKAN TAMBAHKAN TAG HBD2015 ya </b>
            </blink>
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="panel" style="padding:10px;">
            <blink>
            <b style="color:red;">UNTUK PERTANYAAN DAN REPORT BUG/ERROR BISA DIKIRIMKAN EMAIL DENGAN SCREEN CAPTURE ERROR/BUG JIKA ADA KE EMAIL info@hondacommunity.net</b>
            </blink>
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="panel" style="padding:10px;">
            <blink>
            <b style="color:red;">ADA TEMPLATE BARU DAN BISA DIGUNAKAN DENGAN MENGGANTI MELALUI <a href="<?php echo site_url('master/klub');?>">MENU MANAJEMEN > DAFTAR KLUB</a> EDIT DAN PILIH TEMPLATE</b><br />
            CONTOH/PREVIEW TEMPLATE KLUB 1 KLIK DISINI <a href="<?php echo site_url('template/hondacommunity/klub');?>" target="_blank"><b>TEMPLATE KLUB 1 --> </b></a><br />
            CONTOH/PREVIEW TEMPLATE KLUB 2 KLIK DISINI <a href="<?php echo site_url('template/hondacommunity/klub2');?>" target="_blank"><b>TEMPLATE KLUB 2 --> </b></a>
            </blink>
        </div>
    </div>
    
    <div class="col-md-8">
    	<div class="panel" style="height:280px;padding:10px;" id="cart_line_utama">
    		
    	</div>
    </div>
    <div class="col-md-4">

        <div class="col-md-6">
            <a class="tile tile-info" href="#">
                <?php echo myNum(get_artikel());?>
                <p>Artikel</p>                            
                <div class="informer informer-default dir-tr"><span class="fa fa-calendar"></span></div>
            </a>
        </div>
        <div class="col-md-6">
            <a class="tile tile-success" href="#">
                <?php echo myNum(get_jumlah_klub());?>
                <p>Klub</p>                            
                <div class="informer informer-default dir-tr"><span class="fa fa-calendar"></span></div>
            </a>
        </div>

        <div class="col-md-6">
            <a class="tile tile-warning" href="#">
                <?php echo myNum(get_jumlah_member());?>
                <p>Member</p>                            
                <div class="informer informer-default dir-tr"><span class="fa fa-users"></span></div>
            </a>
        </div>
        <div class="col-md-6">
            <a class="tile tile-success" href="#">
                <?php echo myNum(get_user());?>
                <p>User</p>                            
                <div class="informer informer-default dir-tr"><span class="fa fa-users"></span></div>
            </a>
        </div>
        
        <div class="col-md-12">
            <div class="panel" style="height:40px;padding:10px;" >
                <marquee>Jumlah Artikel Hari Ini <b><?php echo get_artikel(date("Y-m-d"));?></b>, Jumlah Member Yang Mendaftar Hari ini adalah <b><?php echo get_jumlah_member(date("Y-m-d"));?></b> </marquee>
            </div>
        </div>         

    </div>

    <div class="col-md-3">
    	<div class="panel" style="height:300px;padding:10px;" id="cart_pie_gender">

    	</div>
    </div>
    <div class="col-md-5">
    	<div class="panel" style="height:300px;padding:10px;" id="cart_bar_klub">

    	</div>
    </div>

    <div class="col-md-4">
        <div class="panel" style="height:300px;padding:10px;">
            <h5 class="heading-form">15 Artikel Terbaru</h5>
            <div style="height:220px;padding:0px;overflow:auto;font-size:12px;margin-left:-10px;">
                <ol>
                <?php
                foreach ((array)get_artikel_list() as $k => $v) {
                    echo "<li><a href='#'>".word_limiter($v->judul,4)."</a> &rarr; <i style='font-size:10px;color:#ccc;'>".myDate($v->tanggal,"d M y")."</i></li>";
                }
                ?>
                </ol>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#panel-content-wrap').removeClass('panel');
    $('#border-header').css('border','none');
}); 
$(function () {

    $('#cart_pie_gender').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
        },
        colors:['#009F9A','#38B8E3','#90B356'],
        title: {
            text: ''
        },
        subtitle: {
            text: '<b>Member Berdasarkan Jenis Kelamin</b>'
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        credits: {
            enabled:false
        },
        exporting:{
            enabled:false
        },
        plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
        series: [{
            type: 'pie',
            name: 'Jenis Kelamin',
            data: [
                <?php 
                foreach ((array)get_pie_gender_member() as $k => $v) {
                   echo $k==0?"":",";
                   echo "['".$jenkel[$v->nama]."',".$v->jumlah."]";
                }?>
            ]
        }]
    });

    $('#cart_line_utama').highcharts({
        
        chart: {
            type: 'spline'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: '<b>Data Artikel Website 30 Hari Terakhir</b>'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: ''
            }
        },
        credits: {
            enabled:false
        },
        exporting:{
            enabled:false
        },
        yAxis: {
            title: {
                text: 'Jumlah'
            },
            min: 0
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e. %b}: {point.y:.f}'
        },

        plotOptions: {
            area: {
                marker: {
                    enabled: true
                },
                fillOpacity:0.3
            },
            enableMouseTracking: false
        },
        legend:{
            enabled:false
        },
        series: [{
            name:'Artikel',
            data: [
                <?php 
                foreach ((array)get_line_byday() as $k => $v) {
                   $koma = $k<count(get_line_byday())-1?",":"";
                   $dm = explode("-", $v->tgl);
                   echo "[Date.UTC(".$dm[0].",".((int)$dm[1]-1).",".((int)$dm[2])."),".$v->jumlah."]".$koma."
                   ";
                }?>
            ]
        }]

    });

    $('#cart_bar_klub').highcharts({
        chart: {
            type: 'column',
            marginLeft:60,
            marginRight:10,
            reflow: true
        },
        title: {
            text: ''
        },
        credits: {
          enabled:false
        },
        exporting:{
          enabled:false
        },
        subtitle: {
            text: '<b>Top 20 Jumlah Artikel Klub</b>'
        },
        plotOptions: {
            column : {
                pointWidth:23
            }
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -35,
                style: {
                    fontSize: '9px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '<b>{point.y:.1f}</b>'
        },
        series: [{
            name: 'Klub',
            data: [<?php
              foreach ((array)get_cart_klub() as $p => $q) {
                echo $p>0?',':'';
                echo "['".$q->nama."',".$q->jumlah."]";
              }
            ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#111',
                align: 'right',
                x: 4,
                y: -20,
                style: {
                    fontSize: '9px',
                    fontFamily: 'Verdana, sans-serif'
                }
            },
            color:'#009F9A'
        }]
    });

});
</script>