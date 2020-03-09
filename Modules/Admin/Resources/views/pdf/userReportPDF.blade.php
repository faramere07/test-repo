<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
        <style type="text/css">
        	h3{
        
				  font-family: 'Helvetica';
	
        	}
		    table, td, th{
		      border: 1px solid #ddd;
		    }
		    table{
		      border-collapse: collapse;
		      width: 100%;
		    }
		    th, td {
		      padding: 5px;
		    }
		    #container{
		      padding: 15px;
		    }

  	  	</style>

<body>
	
  	<h3>Showing added 
  		@if($type)
  			'{{$type}}'
  		@endif
  		accounts created from  {{$min}} - {{$max}}</h3>
  		<br>
	<table>
		<tr>
        	<th>Profile Picture</th>
			<th>Username</th>
			<th>First Name</th>
			<th>Middle name</th>
			<th>Last Name</th>
			<th>User Type</th>
		</tr>

		@foreach($query as $q)
		<tr>
			<td>{{$q->project->project_name}}</td>
			<td>{{$q->user_name}}</td>
			<td>{{$q->first_name}}</td>
			<td>{{$q->middle_name}}</td>
			<td>{{$q->last_name}}</td>
			<td>{{$q->type_name}}</td>
		</tr>
		@endforeach
	</table>
	<div>
		Report generated by: {{$gen->first_name}}
	</div>
</body>
</html>