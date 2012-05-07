<?php $this->load->js(base_url().'themes/mainframe/js/unit_testing.js');?>

<div id="top_navbar">
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/helpers">Helpers</a>|
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/models">Models</a>|
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/libraries">Libraries</a>|
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/plugins">Plugins</a>
</div>

<div id="breadcrumb">
	<h1><?php echo humanize($this->uri->segment(3)).': '.$this->uri->segment(4);?></h1>
</div>

<div id="contents">
	<div id="file_list">
	<?php if(isset($files)):?>
	<?php foreach($files as $file):?>
		<ul>
			<?php switch($this->uri->segment(4)){
				case 'helpers':
					?><li><a href="<?php echo base_url();?>mainframe/dev_tools/helpers_report/<?php echo $file['path'];?>" class="filename"><?php echo $file['path'];?></a></li><?php 
					break;
				case 'models':
					?><li><a href="<?php echo base_url();?>mainframe/dev_tools/models_report/<?php echo $file['path'];?>" class="filename"><?php echo $file['path'];?></a></li><?php
					break;
				case 'libraries':
					?><li><a href="<?php echo base_url();?>mainframe/dev_tools/libraries_report/<?php echo $file['path'];?>" class="filename"><?php echo $file['path'];?></a></li><?php
					break;
				default:
					?><li><a href="javascript:;" class="filename"><?php echo $file['path'];?></a></li><?php
					break;
			}?>
		</ul>
	<?php endforeach;?>
	<?php endif;?>
	</div>
	<?php if(isset($reports)):?>
	<div id="unit_test_report">
		<?php foreach($reports as $report):?>
		<h4><?php echo reset(explode('(',$report['Test Name'])).'()';?></h4>
		<table style="width:100%; font-size:small; margin:0 0 10px 0; border-collapse:collapse; border:1px solid #CCC;">
			<?php foreach($report as $key=>$value):?>
			<tr>
				<td><?php echo $key?></td>
				<td style="<?php if($key === 'Result'){if($value=='Passed'){echo 'color:green;';}else{echo 'color:red;';}}?>"><?php echo $value;?></td>
			</tr>
			<?php endforeach;?>
		</table>
		<?php endforeach;?>
	</div>
	<?php endif;?>
	
</div>