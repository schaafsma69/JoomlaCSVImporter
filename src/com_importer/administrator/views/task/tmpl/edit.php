<?php
/**
 * @version     1.0.0
 * @package     com_importer
 * @copyright   Copyright (C) 2015. Alle rechten voorbehouden.
 * @license     GNU General Public License versie 2 of hoger; Zie LICENSE.txt
 * @author      Pieter <schaafsma69@gmail.com> - http://joomla.s-core.nl
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_importer/assets/css/importer.css');
$document->addScript('components/com_importer/assets/js/angular.min.js');
//$document->addScript('components/com_importer/assets/js/angular-chosen.min.js');
$document->addScript('components/com_importer/assets/js/ng_taskedit.js');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    
    js(document).ready(function() {
    	js.ajax({
    		url:	'index.php',
    		type:	'post',
    		data: { 'option': 'com_importer',
    				'task'  : 'webservices.getDBTables' },	
    		success: function(result){
    			header	= result['data'];
    			for(var i=0;i< header.length;i++) {
    				item = header[i].table_name;
    				js("#dblist").append( js('<option></option>', {"text":item}).val(item) )
    			}
//    			js('#dblist').trigger("liszt:updated");
    		}
    	});	
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'task.cancel') {
            Joomla.submitform(task, document.getElementById('task-form'));
        }
        else 
        {
            if (task != 'task.cancel' && document.formvalidator.isValid(document.id('task-form'))) {
                Joomla.submitform(task, document.getElementById('task-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_importer&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="task-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_IMPORTER_TITLE_TASK', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('definition'); ?></div>
						<div class="controls">
							<?php echo $this->form->getInput('definition'); ?>
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#defineTask">Edit</button>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('last_run'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('last_run'); ?></div>
					</div>
                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

<div class="modal fade" id="defineTask" tabindex="-1" role="dialog" aria-labelledby="defineTaskLabel">
	<div class="modal-dialog" ng-app="taskeditApp">
		<div class="modal-content" ng-controller="taskeditCtrl">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Task Wizard</h4>
			</div>
			<div class="navbar">
  				<div class="navbar-inner">
				    <ul class="nav">
						<li role="presentation" class="active">
							<a href="#taskfile" aria-controls="taskfile" role="tab" data-toggle="tab">Task File</a>
						</li>
						<li role="presentation">
							<a href="#cvsfile" aria-controls="cvsfile" role="tab" data-toggle="tab">CVS</a>
						</li>
						<li role="presentation">
							<a href="#database" aria-controls="database" role="tab" data-toggle="tab">Database</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="modal-body">
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="taskfile">
						<div class="breadcrumb">XML Task definition</div>
						<textarea name="taskfile" class="span12">{{xml_file}}</textarea>
						<div>
							<input class="btn btn-primary btn-file" type="submit" ng-click="getxml()" value="Export" />
						</div>
						<br/>
						<div class="breadcrumb">CSV fields</div>
						<p>If you want to adjust the CSV fields in the task file, use the appropriate action and select a CSV file to complete this action with.</p>
						<div>
							<div class="btn-group">
								<a class="btn btn-primary dropdown-toggle btn-file" data-toggle="dropdown" href="#">
  									Modify CSV with&hellip; <span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a ng-click="getcsvfile(1)">Replace</a></li>
									<li><a ng-click="getcsvfile(0)">Update</a></li>
  								</ul>
							</div>
							<input type="file" id="uploadcsvfile" class="inputfile" on-read-file="cvsfilechange($fileContent)" />
						</div>
						<br/>
						<div class="breadcrumb">Database fields</div>
						<p>Select the database tables that you want to use in your task.</p>
						<select name="dblist" id="dblist" class="span8" multiple ></select>
						<p>Click on the appropriate action to modify the current set.</p>
						<div>
							<div class="btn-group">
  								<a class="btn btn-primary dropdown-toggle btn-select" data-toggle="dropdown" href="#">
  									Modify DB Tables <span class="caret"></span>
  								</a>
  								<ul class="dropdown-menu">
									<li><a ng-click="dbupdate(0)">Replace</a></li>
									<li><a ng-click="dbupdate(1)">Update</a></li>
  								</ul>
							</div>
						</div>							
					</div>
					<div role="tabpanel" class="tab-pane" id="cvsfile">
						<div class="panel panel-default" id="cf_{{csvfield.id | cleanId}}" ng-repeat="csvfield in csvfields">
							<div class="panel-heading">
								<span class="panel-title" ng-click='toggle(csvfield.id);'>{{csvfield.name}}</span>
								<button class="btn panel-link"><i class="icon-remove" ng-click='removecsv(csvfield.id);'></i></button>
							</div>
							<div class="panel-body content" style="display:none">
								<div class="row">
									<div class="span4 offset1">
										<label>Id</label>
									</div>
									<div class="span3">
										<input type="text" ng-model="csvfield.id" />
									</div>
								</div>
								<div class="row">
									<div class="span4 offset1">
										<label>Name</label>
									</div>
									<div class="span3">
										<input type="text" ng-model="csvfield.name" />
									</div>
								</div>
								<div class="row">
									<div class="span4 offset1">
										<label>Format</label>
									</div>
									<div class="span3">
										<input type="text" ng-model="csvfield.format" />
									</div>
								</div>
								<div class="row">
									<div class="span4 offset1">
										<label>Is empty allowed</label>
									</div>
									<div class="span3">
										<input type="text" ng-model="csvfield.is_empty_allowed"/>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="database">
						<div class="panel panel-default" id="cf_{{table.dbname | cleanId}}" ng-repeat="table in tables">
							<div class="panel-heading">
								<span class="panel-title"  ng-click='toggle(table.dbname);'>{{table.dbname}}</span>
								<button class="btn panel-link"><i class="icon-remove" ng-click='removedb(table.dbname);'></i></button>
							</div>
							<div class="panel-body content" style="display:none">
								<div class="row">
									<div class="span3 offset1">
										<label class="checkbox">Conditional Insert</label>
									</div>
									<div class="span1">
										<input type="checkbox" ng-model="table.cond_insert"></input>
									</div>
									<div class="span3">
										<label class="checkbox">Use TimeStamp</label>
									</div>
									<div class="span1">
										<input class="" type="checkbox" ng-model="table.use_timestamp"></input>
									</div>
								</div>
								<div class="row">
									<div class="span3 offset1">
										<label class="checkbox">Use Updated</label>
									</div>
									<div class="span1">
										<input class="" type="checkbox" ng-model="table.use_updated"></input>
									</div>
									<div class="span3">
										<label class="checkbox">Cleanup after</label>
									</div>
									<div class="span1">
										<input class="" type="checkbox" ng-model="table.cleanup_after"></input>
									</div>
								</div>
								<div class="row">
									<div class="span3 offset1">
										<label class="checkbox">Catch all</label>
									</div>
									<div class="span1">
										<input class="" type="checkbox" ng-model="table.catch_all"></input>
									</div>
									<div class="span3">
										<label class="checkbox">Delete non updated</label>
									</div>
									<div class="span1">
										<input class="" type="checkbox" ng-model="table.delete_non_updated"></input>
									</div>
								</div>
								
						        <div class="well" ng-repeat="column in table.columns">
						        	<div class="span9 offset1 panel-subtitle">{{column.fieldname}}</div>
							        <div class="row">
								        <div class="span4 offset1">
								        	<label>CVS field</label>
								        </div>
								    	<div class="span3">
								    		<select ng-options="option.id as option.name for option in csvfields" ng-model="column.csvname" >
								    			<option value="">Select field</option>
								    		</select>
								    	</div>
							    	</div>
							        <div class="row">
								        <div class="span4 offset1">
								        	<label>Default value</label>
								        </div>
								    	<div class="span3">
											<input class="" type="text" ng-model="column.default_val"></input>
								    	</div>
							    	</div>
						        </div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="cvssave">Save changes</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->