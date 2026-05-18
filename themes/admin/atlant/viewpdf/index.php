<html>
  <head>
    <title>File <?php echo $file?></title>
    <script type="text/javascript" src="<?php echo themeUrl();?>/viewpdf/avg_ls_dom.js"></script>
	<script type="text/javascript" src="<?php echo themeUrl();?>/viewpdf/pdfobject.js"></script>
    <script type="text/javascript">

      window.onload = function (){
        var myPDF = new PDFObject({ url: "<?php echo base_url()?>assets/collections/file/<?php echo $file?>" }).embed();
      };

    </script>
  </head>
 
  <body>
    <p>If it appears you don't have Adobe Reader or PDF support in this web
    browser. <a href="<?php echo base_url()?>assets/collections/file/<?php echo $file?>">Click here to download the PDF</a></p>
  </body>
</html>