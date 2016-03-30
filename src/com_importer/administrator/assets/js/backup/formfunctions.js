function load_behaviour() {
	
	js("#cvssave").on("click", function(){ js("#jform_definition").val(taskDefinition.export()); });
	
	js("#btn-csv-replace").on("click", function() {js("input[name='csv-replace']").trigger( "click" );});

	js("#csv-replace").on("change", function() {load_csv(false);});
	
	js("#btn-csv-update").on("click", function() {js("input[name='csv-update']").trigger( "click" );});

	js("#csv-update").on("change", function() {load_csv(true);});
	
	js("#btn-db-update").on("click", function() {load_columns(true);});
	
	js("#btn-db-replace").on("click", function() {load_columns(false);});
	
	load_tables();

}

function load_csv(reset) {
	if (reset) {
		files = js("#csv-update").prop('files');	
	} else {
		files = js("#csv-replace").prop('files');
	}
    
    if (files.length>0) {
        var file = files[0];
    	var reader = new FileReader();
    	reader.onload	= function(e) {
    		var text	= reader.result;
    		js.ajax({
				url:	'index.php',
				type:	'post',
				data: { 'option': 'com_importer',
						'task'  : 'webservices.csv2array', 
						'data'  : text },	
				success: function(result){
	    			header	= result['data'][0];
	    			for(var i=0;i< header.length;i++) {
	    				key	= header[i];
	    				taskDefinition.addCSVField(key);
	    			}
	    			resetCSVFields(reset);
				}
    		});
    	}
        reader.readAsText(file);
    }
}

function load_tables() {
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
			js('#dblist').trigger("liszt:updated");
		}
	});	
}

function load_columns(reset) {
	table_names = js("#dblist").val();	
	// Add tables to def
	for(var i=0;i< table_names.length;i++) {
		taskDefinition.addTable( table_names[i] );
	}
	// Get Columns
	js.ajax({
		url:	'index.php',
		type:	'post',
		data: { 'option': 'com_importer',
				'task'  : 'webservices.getDBColumns',
				'data'	: table_names },	
		success: function(result){
			columns	= result['data'];
			for(var i=0;i< columns.length;i++) {
				taskDefinition.addTableField(	columns[i].TABLE_NAME,
												columns[i].COLUMN_NAME,
												columns[i].DATA_TYPE,
												(columns[i].COLUMN_KEY == "PRI"),
												(columns[i].IS_NULLABLE == "YES"),
												columns[i].COLUMN_DEFAULT );
			}
			resetDBFields(reset);
		}
	});	
}

function resetDBFields(replace) {
	dbtables = taskDefinition.getDBFields();
	dbtables.forEach(function(item){
		tablename		= item[0];
		order			= item[1];
		cond_insert		= item[2];
		use_timestamp	= item[3];
		use_updated		= item[4];
		cleanup_after	= item[5];
		catch_all		= item[6];
		delete_non_updated = item[7];
		fields			= item[8];
		
		if (js("#dbf_"+tablename).length>0) {
			if (replace) updateDBTable(tablename, order, cond_insert, use_timestamp, use_updated, cleanup_after, catch_all, delete_non_updated, fields);
		} else {
			addDBTable(tablename, order, cond_insert, use_timestamp, use_updated, cleanup_after, catch_all, delete_non_updated, fields)
		}
	});
}

function addDBTable(tablename, order, cond_insert, use_timestamp, use_updated, cleanup_after, catch_all, delete_non_updated, fields) 
{
	field	= js( "<div></div>", { "class": "panel panel-default", "id": "cf_"+tablename } );
	header	= js( "<div></div>", { "class": "panel-heading" });
	title   = js( "<span></span>", {"class":"panel-title", "text": tablename, click: function(){ js( "#cf_"+tablename+" div.content" ).toggle(); } });
	button	= js( "<button></button>", {"class":"btn panel-link", click: function(){ removeDBTable(tablename); } }).append( js( "<i></i>", {"class":"icon-remove"} ));
	field.append( header.append(button).append(title) );	
	
	content	= js( "<div></div>", {	"class":"panel-body content", "style":"display:none"} );
	
	content.append( js("<label></label", { "class":"checkbox", "text":"Conditional insert" }).
			append( js("<input></input>", { "class":"", "type":"checkbox", "name":"cond_insert" } ).val(cond_insert)));
	content.append( js("<label></label", { "class":"checkbox", "text":"Use TimeStamp"  }).
			append( js("<input></input>", { "class":"", "type":"checkbox", "name":"use_timestamp" } ).val(use_timestamp)));
	content.append( js("<label></label", { "class":"checkbox", "text":"Use Updated"  }).
			append( js("<input></input>", { "class":"", "type":"checkbox", "name":"use_updated" } ).val(use_updated)));
	content.append( js("<label></label", { "class":"checkbox", "text":"Cleanup after" }).
			append( js("<input></input>", { "class":"", "type":"checkbox", "name":"cleanup_after" } ).val(cleanup_after)));
	content.append( js("<label></label", { "class":"checkbox", "text":"Catch all" }).
			append( js("<input></input>", { "class":"", "type":"checkbox", "name":"catch_all" } ).val(catch_all)));
	content.append( js("<label></label", { "class":"checkbox", "text":"Delete non updated" }).
			append( js("<input></input>", { "class":"", "type":"checkbox", "name":"delete_non_updated" } ).val(delete_non_updated)));
	
	cvsfields = taskDefinition.getCSVFields();
	dboptions = js("<select/>", {"name":"cvsval"} );
	dboptions.append( js("<option />",{"val":-1,"text":"no csv field"}));
	cvsfields.forEach( function(cvsitem){ dboptions.append(js("<option/>", {"val":cvsitem[0],"text":cvsitem[2]} )); });
	
	fields.forEach( function(item) {
		db_name		= item[0];
		cvs_name	= item[1];
        type		= item[2];
        is_primkey	= item[3];
        is_required = item[4];
        default_val = item[5];
        
        field_val = db_name+" ( "+ type + ((is_required)?" NOT NULL":"") + " ) ";
        field_def = js("<span></span>", { "text": field_val });
        if (is_primkey) field_def.append( js( "<img>", {"class":"img-rounded pull-right", "src":"./components/com_importer/assets/images/primkey.png"} ));
        				
        dbfield = js("<div/>", {"class":"well"});
        dbfield.append( js("<div/>", {"class":"span9 offset1 panel-subtitle"}).append(field_def)); 
        
        label	= js("<div/>", {"class":"span4 offset1"}).append(js("<label></label>").text("Default value"));
    	input	= js("<div/>", {"class":"span3"}).append(js("<input/>", { "type":"text", "name":"defval", "val":default_val,
    				on:{focusout:function(){changeDBfield(db_name,"defval");}} } ) );
    	dbfield.append( js( "<div></div>", { "class": "row" }).append(label).append(input) );
    	
        label		= js("<div/>", {"class":"span4 offset1"}).append(js("<label></label>").text("CVS field"));
        
        selector	= dboptions.clone().val(cvs_name).on("focusout", function(){ changeDBfield( db_name, "cvsval" );});
    	input		= js("<div />", {"class":"span3"}).append(selector);
    	dbfield.append( js( "<div/>", { "class": "row" }).append(label).append(input) );
        content.append(dbfield);
	});
	
	field.append( content );
	js("#db_fields").append( field );
}

function updateDBTable(tablename, order, cond_insert, use_timestamp, use_updated, cleanup_after, catch_all, delete_non_updated, fields) {
	fields.forEach( function(field) {
		cvs_name	= field[0];
        type		= field[1];
        is_primkey	= field[2];
        is_required = field[3];
        default_val = field[4];
        
        
	});
}



function resetCSVFields(replace) {
	fields = taskDefinition.getCSVFields();
	fields.forEach(function(item){
		int_id	= item[0];
		fid		= item[1];
		fname	= item[2];
		fformat = item[3];
		fiea	= item[4];

		if (js("#cf_"+int_id).length>0) {
			if (replace) updateCSVfield(int_id, fid, fname, fformat, fiea);
		} else {
			addCSVfield(int_id, fid, fname, fformat, fiea)
		}
	});
}

function addCSVfield(int_id, fid, fname, fformat, fiea) {
	field	= js( "<div></div>", {	"class": "panel panel-default", 
									"id": "cf_"+int_id } );
	header	= js( "<div></div>", {	"class": "panel-heading" });
	title   = js( "<span></span>", {"class":"panel-title",
									"text": fname,
									click: function(){ js( "#cf_"+int_id+" div.content" ).toggle(); } 
									});
	button	= js( "<button></button>", {"class":"btn panel-link",
										click: function(){ removeCSVfield(int_id); }
										}).append( js( "<i></i>", {"class":"icon-remove"} ));
	field.append( header.append(button).append(title) );

	content	= js( "<div></div>", {	"class":"panel-body content", "style":"display:none"} );
	
	label	= js("<div></div>", {"class":"span4 offset1"}).append(js("<label></label>").text("Id"));
	input	= js("<div></div>", {"class":"span3"}).append(js("<input />", {"type": "text", "name":"id",
		on:{focusout:function(){ changeCSVfield(int_id, "id" ); }}}).val(fid));
	content.append(js( "<div></div>", { "class":"row" }).append(label).append(input) );
	
	label	= js("<div></div>", {"class":"span4 offset1"}).append(js("<label></label>").text("Name"));
	input	= js("<div></div>", {"class":"span3"}).append(js("<input />", {"type": "text", "name":"name",
				on:{focusout:function(){ changeCSVfield(int_id, "name" ); }}}).val(fname));
	content.append(js( "<div></div>", { "class":"row" }).append(label).append(input) );
	
	label	= js("<div></div>", {"class":"span4 offset1"}).append(js("<label></label>").text("Format"));
	input	= js("<div></div>", {"class":"span3"}).append(js("<input />", { "type":"text", "name":"format",
				on:{focusout:function(){ changeCSVfield(int_id, "format" ); }}}).val(fformat));
	content.append( js( "<div></div>", { "class": "row" }).append(label).append(input) );
	
	label	= js("<div></div>", {"class":"span4 offset1"}).append(js("<label></label>").text("Is empty allowed"));
	input	= js("<div></div>", {"class":"span3"}).append(js("<input />", {"type": "text", "name":"is_empty_allowed",
				on:{focusout:function(){ changeCSVfield(int_id, "is_empty_allowed" ); }}}).val(fiea));
	content.append( js( "<div></div>", { "class": "row" }).append(label).append(input) );
	
	field.append( content );

	js("#csv_fields").append( field );
}

function changeCSVfield( int_id, fieldtype ) {
	oldVal = taskDefinition.getCSVFieldProp(int_id, fieldtype);
	selector= "#cf_"+int_id+" input[name='"+fieldtype+"']";
	newVal = js( selector ).val();
	if (oldVal!=newVal) {
		result = taskDefinition.setCSVFieldProp(int_id, fieldtype, newVal);
		if (!result) console.log("error");
	}
}

function changeDBfield( dbtable, dbfield ) {

}

function removeCSVfield(int_id) {
	taskDefinition.removeCSVField(int_id);
	dropCSVfield(int_id);
}

function dropCSVfield(int_id) {
	js("#cf_"+int_id).remove();
}

function updateCSVfield(int_id, fid, fname, fformat, fiea) {
	element = js("#cf_"+int_id);
	element.find( "input[name='int_id']" ).val( int_id );
	element.find( "input[name='id']" ).val( fid );
	element.find( "input[name='name']" ).val( fname );
	element.find( "input[name='format']" ).val( fformat );
	element.find( "input[name='is_empty_allowed']" ).val( fiea );
}