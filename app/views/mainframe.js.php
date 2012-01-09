<script type="text/javascript">
	// Allows the use of anchors in server side code
	var anchor_part = location.href.split("#");

	if ((anchor_part.length > 1) && (anchor_part[1] != "<?php echo $this->input->cookie('anchor');?>")) {
		document.cookie='anchor='+anchor_part[1];
	    window.location.reload();
	}

	// Just for convenience
	var base_url = '<?php echo base_url();?>';
</script>