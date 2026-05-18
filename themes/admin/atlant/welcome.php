<div class="well">      
        <center>
		<legend><h1><b>Selamat datang di International SOS Managed Health Care</b></h1></legend>
		<h4>Silakan klik menu dashboard di sudut kiri atas untuk memulai!</h4>
	</center>
        <?php if (isset($quote)) { ?>
                <blockquote>
                        <p><h2><?php echo $quote->quote; ?></h2></p>
                        <h3><footer><cite title="<?php echo $quote->author; ?>"><?php echo $quote->author; ?></cite></footer></h3>
                </blockquote>
        <?php } ?>
</div>
