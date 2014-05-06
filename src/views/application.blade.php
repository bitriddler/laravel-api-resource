<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PHP Framework</title>
    <style>
        input, textarea{
            width:300px;
            border:1px solid #333;
            padding:10px;
        }

        input[type=submit] {
            width:323px;
        }
    </style>
</head>
<body>

<h1 style="text-align: center; color:#AAA;">
    API: Application Creation Page
</h1>

<form action="{{ $baseUrl }}/create" method="POST" style="text-align: center">
    <input placeholder="key" type="text" name="key"/><br/>
    <input placeholder="secret" type="text" name="secret"/><br/>
    <textarea placeholder="permissions" name="permissions" rows="10" cols="60"></textarea><br/>
    <input type="submit" value="Create"/>
</form>


<div>
    @foreach($applications as $application)
    <div style="background:#EEE; padding:20px; margin-top:10px; text-align: center">
        <a style="color:#900;" href="{{ $baseUrl }}/delete/{{ $application->id }}">Delete</a><Br/>
        <form action="{{ $baseUrl }}/update/{{ $application->id }}" method="POST">
            <input type="text" name="key" value="{{ $application->key }}"/><br/>
            <input type="text" name="secret" value="{{ $application->secret }}"/><br/>
            <textarea name="permissions" id="" cols="30" rows="10">{{ $application->getPermissionsString() }}</textarea><br/>
            <input type="submit" value="Update" />
        </form>
    </div>
    @endforeach
</div>
</body>
</html>