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
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_importer/assets/css/importer.css');
$document->addScript('components/com_importer/assets/js/taskdefinition.js');
$document->addScript('components/com_importer/assets/js/formfunctions.js')

?>
<script type="text/javascript">
	var taskDefinition = new ImportTask();
    js = jQuery.noConflict();
    js(document).ready(function() {
    	load_behaviour();
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
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Task Wizard</h4>
			</div>
			<div class="navbar">
  				<div class="navbar-inner">
				    <ul class="nav">
						<li class="active"><a href="#tab1" data-toggle="tab">Start</a></li>
						<li><a href="#tab2" data-toggle="tab">CSV Fields</a></li>
						<li><a href="#tab3" data-toggle="tab">DB Fields</a></li>
					</ul>
				</div>
			</div>
			<div class="modal-body">
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<div class="breadcrumb">CSV fields</div>
						<p>If you want to adjust the CSV fields in the task file, use the appropriate action and select a CSV file to complete this action with.</p>
						<div>
							<span class="btn btn-primary btn-file" id="btn-csv-replace">Replace CSV with&hellip;</span>
							<input type="file" name="csv-replace" id="csv-replace" class="input-file">
							<span class="btn btn-primary btn-file" id="btn-csv-update">Update CSV with&hellip;</span>
							<input type="file" name="csv-update" id="csv-update" class="input-file">
						</div>
						<br/>
						<div class="breadcrumb">Database fields</div>
						<p>Select the database tables that you want to use in your task.</p>
						<select name="dblist" id="dblist" class="span8" multiple ></select>
						<p>Click on the appropriate action to modify the current set.</p>
						<div>
							<span class="btn btn-primary btn-select" id="btn-db-replace">Replace DB Tables</span>
							<span class="btn btn-primary btn-select" id="btn-db-update">Update DB Tables</span>
						</div>
					</div>
					<div class="tab-pane" id="tab2">
						<p>Define input fields as provided by the CSV input file.</p>
						<hr>
						<div id="csv_fields"></div>
					</div>
					<div class="tab-pane" id="tab3">
						<p>Define the DB Fields and link to CSV input.</p>
						<div id="db_fields"></div>
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