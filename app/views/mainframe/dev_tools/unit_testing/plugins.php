<?php $this->load->js(base_url().'themes/mainframe/js/unit_testing.js');?>
<div id="top_navbar">
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/helpers">Helpers</a>
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/models">Models</a>
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/libraries">Libraries</a>
	<a href="<?php echo base_url();?>mainframe/dev_tools/unit_testing/plugins">Plugins</a>
</div>

<div id="breadcrumb">
	<h1>Plugins</h1>
</div>

<div id="contents">
	<div id="file_list">
	<?php foreach($files as $plugin_name=>$file):?>
		<?php $plugin_path = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'].'/'.APPPATH.'plugins/'.$plugin_name);?>
		<ul>
			<li><a href="javascript:;" class="filename"><?php echo $plugin_name;?></a></li>
			<li style="display:none;">
				<ul>
					<li>
						<?php if($file):?>
						<a href="javascript:;">Helpers</a>
						<ul>
							<?php foreach($file as $content):?>
								<?php if(starts_with($content, $plugin_path.'\\helpers')):?>
									<li>
										<a class="p_filename" href="<?php echo base_url();?>mainframe/dev_tools/helpers_report<?php echo str_replace($plugin_path.'\\helpers', '', $content);?>/<?php echo $plugin_name;?>"><?php echo str_replace($plugin_path.'\\helpers\\', '', $content);?></a>
										<ul style="display:none;">
											<?php foreach($functions[$plugin_name] as $row):?>
												<?php if($row['type']==='helper'):?>
												<?php foreach($row['functions'] as $function):?>
													<li><?php echo $function;?></li>
												<?php endforeach;?>
												<?php endif;?>
											<?php endforeach;?>
										</ul>
									</li>
								<?php endif;?>
							<?php endforeach;?>
						</ul>
						<?php endif;?>
					</li>
					<li>
						<?php if($file):?>
						<a href="javascript:;">Models</a>
						<ul>
							<?php foreach($file as $content):?>
								<?php if(starts_with($content, $plugin_path.'\\models')):?>
									<li>
										<a class="p_filename" href="<?php echo base_url();?>mainframe/dev_tools/models_report<?php echo str_replace($plugin_path.'\\models', '', $content);?>/<?php echo $plugin_name;?>"><?php echo str_replace($plugin_path.'\\models\\', '', $content);?></a>
										<ul style="display:none;">
											<?php foreach($functions[$plugin_name] as $row):?>
												<?php if($row['type']==='model'):?>
												<?php foreach($row['functions'] as $function):?>
													<li><?php echo $function;?></li>
												<?php endforeach;?>
												<?php endif;?>
											<?php endforeach;?>
										</ul>
									</li>
								<?php endif;?>
							<?php endforeach;?>
						</ul>
						<?php endif;?>
					</li>
					<li>
						<?php if($file): ?>
						<a href="javascript:;">Libraries</a>
						<ul>
							<?php foreach($file as $content):?>
								<?php if(starts_with($content, $plugin_path.'\\libraries')):?>
									<li>
										<a class="p_filename" href="<?php echo base_url();?>mainframe/dev_tools/libraries_report<?php echo str_replace($plugin_path.'\\libraries', '', $content);?>/<?php echo $plugin_name;?>"><?php echo str_replace($plugin_path.'\\libraries\\', '', $content);?></a>
										<ul style="display:none;">
											<?php foreach($functions[$plugin_name] as $row):?>
												<?php if($row['type']==='library'):?>
												<?php foreach($row['functions'] as $function):?>
													<li><?php echo $function;?></li>
												<?php endforeach;?>
												<?php endif;?>
											<?php endforeach;?>
										</ul>
									</li>
								<?php endif;?>
							<?php endforeach;?>
						</ul>
						<?php endif;?>
					</li>
				</ul>
			</li>
		</ul>
	<?php endforeach;?>
	</div>
</div>