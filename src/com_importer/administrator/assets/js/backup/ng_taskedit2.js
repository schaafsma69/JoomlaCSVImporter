// create the module and name it scotchApp
var taskeditApp = angular.module('taskeditApp', []);

taskeditApp.filter('cleanId', function() {
    return function(id) { return cleanUp(id) };
});

taskeditApp.directive('onReadFile', function ($parse) {
	return {
		restrict: 'A',
		scope: false,
		link: function(scope, element, attrs) {
			var fn = $parse(attrs.onReadFile);
			element.on('change', function(onChangeEvent) {
				var reader = new FileReader();
				reader.onload = function(onLoadEvent) {
					scope.$apply(function() {
						fn(scope, {$fileContent:onLoadEvent.target.result});
					});
				};
				reader.readAsText((onChangeEvent.srcElement || onChangeEvent.target).files[0]);
			});
		}
	};
});

taskeditApp.controller('taskeditCtrl', function($scope, $http) {
	$scope.xml_file = '';
	$scope.csv_file = '';
	$scope.fields	= [];
	$scope.tables	= [{dbname:			"Table 1",
		        		order:			0,
		        		cond_insert:	true,
		        		use_timestamp:	false,
		        		use_updated :	false,
		        		cleanup_after:	false,
		        		catch_all:		false,
		        		delete_non_updated:false,
		        		fields:[{	fieldname:	"Code",
				              		cvsname:	"",
				              		fieldtype:	"VARCHAR(20)",
				              		is_primkey:	true,
				              		is_required:true,
				              		default_val:""
		        				},
		        				{	fieldname:	"FirstName",
				              		cvsname:	"",
				              		fieldtype:	"VARCHAR(20)",
				              		is_primkey:	false,
				              		is_required:false,
				              		default_val:""
		        				},
		        				{	fieldname:	"LastName",
				              		cvsname:	"",
				              		fieldtype:	"VARCHAR(20)",
				              		is_primkey:	false,
				              		is_required:false,
				              		default_val:""
		        				},
		        				{	fieldname:	"MailAdres",
				              		cvsname:	"",
				              		fieldtype:	"VARCHAR(20)",
				              		is_primkey:	false,
				              		is_required:false,
				              		default_val:""
		        				},
		        				{	fieldname:	"MobileNumber",
				              		cvsname:	"",
				              		fieldtype:	"VARCHAR(20)",
				              		is_primkey:	false,
				              		is_required:false,
				              		default_val:""
		        				}]
						}];

	$scope.removeCSVFields = function() {
		$scope.fields = [];
	}
	
	$scope.getcsvfile = function(type) {
        $scope.csvfiletype = type;
        // Make sure same file can be read again.
        angular.element('#uploadcsvfile').val(null); 
        // Trigger the read file box to ask for a file
        angular.element('#uploadcsvfile').trigger('click');
	};
	
	$scope.removedb = function( dbname ) {
		// TODO
	}
	
	$scope.dbupdate = function(type) {
		// TODO
	};
	
	$scope.removecsv = function( csvid ) {
		for(var i=0;i<$scope.fields.length;i++) {
			if ($scope.fields[i].id == csvid) {
				$scope.fields.splice(i,1);
				return;
			}
		}
	}
	
	$scope.addCSVField = function(name) {
		for(var i=0;i<$scope.fields.length;i++) {
			if ($scope.fields[i].id == name) return;
		}
		var csv_object = { id:	name, name:	name, format: "text", is_empty_allowed: true};
		$scope.fields.push( csv_object );
	}
	
	$scope.getxml = function() {
		
		str_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><definition><input><fields>";
		
		for(var i=0;i<$scope.fields.length;i++) {
			field = $scope.fields[i];
			str_xml += 	"<field id=\""+field.id+"\"><is_empty_allowed>"+((field.is_empty_allowed)?"true":"false")+"</is_empty_allowed>" +
						"<name>"+field.name+"</name>" + ((this.format!="")?"<format>"+field.format+"</format>":"<format/>") + "</field>";
		}
		
		str_xml += "</fields></input><output><tables>";
		
		for(var i=0;i<$scope.tables.length;i++) {
			element = $scope.tables[i];
			str_xml += 	"<table name=\""+ element.dbname +"\">" +
						"<catch_all>" + ((element.catch_all)?"true":"false") + "</catch_all>" +
						"<order>" + element.order + "</order>" +
						"<cond_insert>" + ((element.cond_insert)?"true":"false") + "</cond_insert>" +
						"<use_timestamp>" + ((element.use_timestamp)?"true":"false") + "</use_timestamp>" +
						"<use_updated>" + ((element.use_updated)?"true":"false") + "</use_updated>" +
						"<delete_non_updated>" + ((element.delete_non_updated)?"true":"false") + "</delete_non_updated>" +
						"<cleanup_after>" + ((element.cleanup_after)?"true":"false") + "</cleanup_after>" + 
						"<fields>";
			for(var j=0;j<element.fields.length;j++) {
				field = element.fields[j];
			
				str_xml +=	"<field><db_name>" + field.fieldname + "</db_name><cvs_name>" + field.cvsname + "</cvs_name>" +
							"<type>" + field.fieldtype + "</type><is_primkey>" + ((field.is_primkey)?"true":"false") + "</is_primkey>" +
							"<is_required>" + ((field.is_required)?"true":"false") + "</is_required>" +
							"<default>" + field.default_val + "</default></field>";
			}
			str_xml += 	"</fields></table>";
		};

		task.xml_file = str_xml + "</tables></output></definition>";
	}
	
	
	$scope.toggle= function(fieldid) {
		angular.element("#cf_"+cleanUp(fieldid)+" div.panel-body").toggle();
	};
	
	
	$scope.cvsfilechange = function(text) {
		var url	= 'index.php?option=com_importer&task=webservices.csv2array';
		var data	= js.param({data : text });
		var config = { headers : { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' } }

		$http.post(url, data, config).then(
			function(result){
				if ($scope.csvfiletype==1) { $scope.removeCSVFields(); }
    			header	= result.data.data[0];
    			for(var i=0;i< header.length;i++) {
    				key	= header[i];
    				$scope.addCSVField(key);
    			}
			}, 
			function(result){
				console.log(result);
			}
		);
	 }
	    		
//	$scope.load_columns = function(type) {	
//		table_names = js("#dblist").val();	
//		// Add tables to def
//		for(var i=0;i< table_names.length;i++) {
//			taskDefinition.addTable( table_names[i] );
//		}
//		// Get Columns
//		js.ajax({
//			url:	'index.php',
//			type:	'post',
//			data: { 'option': 'com_importer',
//					'task'  : 'webservices.getDBColumns',
//					'data'	: table_names },	
//			success: function(result){
//				columns	= result['data'];
//				for(var i=0;i< columns.length;i++) {
//					taskDefinition.addTableField(	columns[i].TABLE_NAME,
//													columns[i].COLUMN_NAME,
//													columns[i].DATA_TYPE,
//													(columns[i].COLUMN_KEY == "PRI"),
//													(columns[i].IS_NULLABLE == "YES"),
//													columns[i].COLUMN_DEFAULT );
//				}
//				resetDBFields(type);
//			}
//		});	
//	};
});

function cleanUp( term ) { return term.replace(" ", "_"); }