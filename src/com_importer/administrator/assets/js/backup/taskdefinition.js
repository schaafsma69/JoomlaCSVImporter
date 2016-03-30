/*!
 * Task definition object. 
 * Imports task defintion, contains fnuctions for maintenance.
 * Version 1.0
 * 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 * Author: Pieter Schaafsma
 */

function ImportTask( ) {
	this.definition = "";
	this.cvs_count	= 0;
	this.input		= new Array();
	this.output		= new Array();
};

ImportTask.prototype.import = function( xmltext ) {
};

ImportTask.prototype.export = function() {
	var definition = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><definition><input><fields>";
	for(var i=0;i<this.input.length;i++) {
		definition += this.input[i].getXML();
	}
	definition += "</fields></input><output><tables>";
	for(var i=0;i<this.output.length;i++) {
		definition += this.output[i].getXML();
	}	
	definition += "</tables></output></definition>";
	return definition;	
};

ImportTask.prototype.addCSVField = function( name ) {
	int_id = this.cvs_count++;
	id = name.replace(/ /g, "_");
	id = id.replace(/["'.:;()//?!#{}+&*^$@]/g, "");
	this.input.push( new CvsField( int_id, id, name ) );
	
};

ImportTask.prototype.removeCSVField = function( f_id ) {
	for(var i=this.input.length-1;i>=0;i--) {
		if (this.input[i].int_id == f_id) {
			this.input.splice(i,1);
		}
	}
};

ImportTask.prototype.getCSVFields = function() {
	fields = [];
	for(var i=0;i<this.input.length;i++) 
	{
		fields.push([this.input[i].int_id,
		             this.input[i].id,
		             this.input[i].name,
		             this.input[i].format,
		             this.input[i].is_empty_allowed ]); 
	}
	return fields;
};

ImportTask.prototype.getDBFields = function() {
	var dbfields = [];
	for(var i=0;i<this.output.length;i++) 
	{
		var fields	= [];
		for(var j=0;j<this.output[i].fields.length;j++) 
		{
			field = this.output[i].fields[j];
			fields.push([ field.db_name,
			              field.cvs_name,
			              field.type,
			              field.is_primkey,
			              field.is_required,
			              field.default_val ]);
		}
		table = this.output[i];
		dbfields.push([table.dbname,
		               table.order,
		               table.cond_insert,
		               table.use_timestamp,
		               table.use_updated,
		               table.cleanup_after,
		               table.catch_all,
		               table.delete_non_updated,
		               fields ]); 
	}
	return dbfields;
};

ImportTask.prototype.getCSVFieldProp = function( f_id, prop ) {
	for(var i=0;i<this.input.length;i++) 
	{
		if (f_id == this.input[i].int_id) 
		{
			switch(prop) 
			{
			    case "id":
			        return this.input[i].id;
			        break;
			    case "name":
			        return this.input[i].name;
			        break;
			    case "format":
			        return this.input[i].format;
			        break;
			    case "isemptyallowed":
			        return this.input[i].is_empty_allowed;
			        break;
			    default:
			    	return false;
			}
		}
	}
	return false;
};

ImportTask.prototype.setCSVFieldProp = function( f_id, prop, val ) {
	found = false;
	for(var i=0;i<this.input.length;i++) 
	{
		if (f_id == this.input[i].int_id) 
		{
			switch(prop) 
			{
			    case "id":
			        this.input[i].id = val;
			        found = true;
			        break;
			    case "name":
			        this.input[i].name = val;
			        found = true;
			        break;
			    case "format":
			        this.input[i].format = val;
			        found = true;
			        break;
			    case "is_empty_allowed":
			        this.input[i].is_empty_allowed = val;
			        found = true;
			        break;
			}
		}
	}
	return found;
};	

ImportTask.prototype.addTable = function( tablename ) {
	for (var i=0;i<this.output.length;i++) {
		if (this.output[i].dbname == tablename ) return;
	}
	this.output.push( new DbTable(tablename) );
};

ImportTask.prototype.addTableField = function( tablename, dbfname, type, is_primkey, is_required, defval  ) {
	for (var i=0;i<this.output.length;i++) {
		if (this.output[i].dbname == tablename ) {
			this.output[i].addField( dbfname, type, is_primkey, is_required, defval );	
		}
	}
};

ImportTask.prototype.removeTableField = function( tablename, fieldname ) {
	
	
};

ImportTask.prototype.setTableFieldProp = function( tablename, fieldname, prop, val) {
	
};

function DbTable( dbname ) {
	this.dbname			= dbname;
	this.order			= 0;
	this.fields			= new Array();
	this.cond_insert	= true;
	this.use_timestamp	= true;
	this.use_updated	= true;
	this.cleanup_after	= true;
	this.catch_all		= false;
	this.delete_non_updated = false;
};

DbTable.prototype.getXML = function() {
	tableXML = "<table name=\""+ this.dbname +"\">" +
				"<catch_all>" + ((this.catch_all)?"true":"false") + "</catch_all>" +
				"<order>" + this.order + "</order>" +
				"<cond_insert>" + ((this.cond_insert)?"true":"false") + "</cond_insert>" +
				"<use_timestamp>" + ((this.use_timestamp)?"true":"false") + "</use_timestamp>" +
				"<use_updated>" + ((this.use_updated)?"true":"false") + "</use_updated>" +
				"<delete_non_updated>" + ((this.delete_non_updated)?"true":"false") + "</delete_non_updated>" +
				"<cleanup_after>" + ((this.cleanup_after)?"true":"false") + "</cleanup_after>" + 
				"<fields>";
	for (var i=0;i<this.fields.length;i++) {
		field = this.fields[i];
		tableXML += field.getXML();
	}
	tableXML += "</table>";
	return tableXML;
};

DbTable.prototype.setOptions = function( c_al, ord, ci, ut, uu, dnu, c_af) {
	this.catch_all		= c_al;
	this.order			= ord;
	this.cond_insert	= ci;
	this.use_timestamp	= ut;
	this.use_updated	= uu;
	this.delete_non_updated = dnu;
	this.cleanup_after	= c_af;	
};

DbTable.prototype.addField = function(dbfname, type, is_primkey, is_required, defval ) {
	this.fields.push( new DbField( dbfname, type, is_primkey, is_required, defval ));
};
		
function DbField( dbfname, type, is_primkey, is_required, defval ) {
	this.db_name	= dbfname;
	this.cvs_name	= "";			// refers to id in cvsField
	this.type		= type;
	this.is_primkey	= typeof is_primkey === "boolean" ? is_primkey : false;
	this.is_required= typeof is_required === "boolean" ? is_required : true;
	this.default_val= typeof defval === "string" ? defval : "NULL";
	this.is_selectkey = false; 
};

DbField.prototype.getXML = function() 
{
	xmlstr = "<field><db_name>" + this.db_name + "</db_name>";
	xmlstr += "<cvs_name>" + this.cvs_name + "</cvs_name>";
	xmlstr += "<type>" + this.type + "</type>";
	xmlstr += "<is_primkey>" + ((this.is_primkey)?"true":"false") + "</is_primkey>";
	xmlstr += "<is_selectkey>" + ((this.is_selectkey)?"true":"false") + "</is_selectkey>";
	xmlstr += "<is_required>" + ((this.is_required)?"true":"false") + "</is_required>";
	xmlstr += "<default>" + this.default_val + "</default></field>";
	return xmlstr;
};

function CvsField( int_id, id, name ) {
	this.int_id = int_id;
	this.id		= id;
	this.name	= name;
	this.format = "";
	this.is_empty_allowed = true;
};

CvsField.prototype.setFormat = function( value ) {
	this.format = (typeof value === "string")?value:"";
};

CvsField.prototype.setIEA = function( value ) {
	this.is_empty_allowed = (typeof value === "boolean")?value:true;
};

CvsField.prototype.getXML = function() {
	return "<field id=\""+this.id+"\">" +
		   "<is_empty_allowed>"+((this.is_empty_allowed)?"true":"false")+"</is_empty_allowed>" +
		   "<name>"+this.name+"</name>" + 
		   ((this.format!="")?"<format>"+this.format+"</format>":"<format/>") +
		   "</field>";
};