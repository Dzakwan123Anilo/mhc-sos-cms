<html>
  <head>
    <title>File <?php echo $_GET['file']?></title>
    <script type="text/javascript" src="avg_ls_dom.js"></script>
	<script type="text/javascript" src="pdfobject.js"></script>
    <script type="text/javascript">

      window.onload = function (){
        var myPDF = new PDFObject({ url: "../assets/collections/file/<?php echo $_GET['file']?>" }).embed();
      };

    </script>
  </head>
 
  <body>
    <p>It appears you don't have Adobe Reader or PDF support in this web
    browser. <a href="../assets/collections/file/<?php echo $_GET['file']?>">Click here to download the PDF</a></p>
  </body>
</html>