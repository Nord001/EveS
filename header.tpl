{if $user->authorized}
<a href='index.php'>Tools home</a>&nbsp;::&nbsp;<a href='index.php?a=lo'>Logout</a>&nbsp;::&nbsp;<a href='options.php'>Options</a><br>
Hello {$user->name}! 
<hr>
{else}
This is area with restricted access, please login:
<form method="post" action='./index.php?a=li'>
Login: <input type='text' name='name'>
Password: <input type='password' name='pass'>
<input type='submit' value='Ok'>
</form>
<hr>
{/if}
