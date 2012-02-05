' ==============================================================
' Vbscript' Name:	BF2PrivateStats.vbs
' Usage:	BF2PrivateStats.vbs [Enable|Disable]
' Author:	The Shadow
' Email:	shadow42@iinet.net.au
' Desc:	This script enables/disables the HOSTS file workaround
'		for BF2 Private Statistics.  It supports dynamic IPs and can
'		Enable/Disable the entry via way of a runtime argument.
'		Execution is silent.  Change the string lookup variables to suit.
' ==============================================================

' Setup Environment
Const FOR_READING = 1
Const FOR_WRITING = 2

Set objFS       = WScript.CreateObject("Scripting.FileSystemObject")
Set objShell    = WScript.CreateObject("Wscript.Shell")
strHostsFile    = objShell.ExpandEnvironmentStrings("%SystemRoot%") & "\system32\drivers\etc\hosts"

strMatchContent = "BF2web.gamespy.com"

' ========================================================================
' String Lookup Variables
' ========================================================================
' EDIT: This Line includes your game server host name/IP address.
'strLookupAddr   = "privatestats.gamespy.host"		' Host Name
strLookupAddr   = "127.0.0.1"		' IP Address
' ========================================================================

' ========================================================================
' Prepare a regular expression object
Set myRegExp = New RegExp
myRegExp.IgnoreCase = True
myRegExp.Global = False
myRegExp.Pattern = "\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b"

Set MatchData = myRegExp.Execute(strLookupAddr)
If MatchData.Count > 0 Then
	' Address is an IP Address
	Set Match = MatchData(0)
	ipAddress = Match.Value
Else
	' Resolve IP Address
	strLookup = "Address:"
	strTmpFile = objShell.ExpandEnvironmentStrings("%Temp%") & "\" & strLookupAddr & ".tmp"
	objShell.Run "%ComSpec% /c nslookup " & strLookupAddr & "> " & strTmpFile, 0, True
	Set objTF = objFS.OpenTextFile(strTmpFile)
		i = 1
		Do Until objTF.AtEndOfStream
			strLine = Trim(objTF.Readline)
			If i > 2 And Left(strLine, len(strLookup)) = strLookup Then
				Set MatchData = myRegExp.Execute(strLine)
				If MatchData.Count > 0 Then
					Set Match = MatchData(0)
					ipAddress = Match.Value
					Exit Do
				End If
			End If
			i = (i + 1)
		Loop
	objTF.Close
	objFS.DeleteFile strTmpFile
End If
	
' Check ipAddress
If Len(ipAddress) > 0 Then
	' Build Output String
	strOutput = ipAddress & Chr(9) & strMatchContent & Chr(9) & Chr(9) & "# BF2 Statistics Redirect"
	
	' Remove Outsmart Permissions
	objShell.Run "SetACL.exe -on ""%SystemRoot%\System32\drivers\etc\HOSTS""  -ot file -actn clear -clr dacl", 0, True
	
	' Get HOSTS Contents
	Set objTS = objFS.OpenTextFile(strHostsFile ,FOR_READING)
	strContents = objTS.ReadAll
	objTS.Close
	
	' Set HOSTS for Writing
	Set objTS = objFS.OpenTextFile(strHostsFile ,FOR_WRITING)
	arrLines  = Split(strContents, vbNewLine)
	
	' Prepare a regular expression object
	Set myRegExp = New RegExp
	myRegExp.IgnoreCase = True
	myRegExp.Global = True
	myRegExp.Pattern = strMatchContent
	
	' Re-write HOSTS file
	bUpdated = False
	For i = 0 To UBound(arrLines) - 1 
		If myRegExp.Test(arrLines(i)) Then
			bUpdated = True
			objTS.WriteLine strOutput
		Else
			objTS.WriteLine arrLines(i)
		End If
	Next
	
	' Check File Updated
	If bUpdated = False Then
		' Insert Reference at end of File
		objTS.WriteLine strOutput
	End If
	objTS.Close
	
	' Outsmart BF2 - Deny Read Permissions on HOSTS file
	objShell.Run "%ComSpec% /c ipconfig /flushdns", 0, True
	objShell.Run "%ComSpec% /c ping " & strMatchContent, 0, True
	objShell.Run "SetACL.exe -on ""%SystemRoot%\System32\drivers\etc\HOSTS"" -ot file -actn ace -ace ""n:S-1-5-32-545;p:read;s:y;m:deny""", 0, True
	
	' For debugging, show the ping this time in the command window
	' objShell.Run "%ComSpec% /c ping " & strMatchContent, 1, True

	' Get Arguments
	Set objArgs = WScript.Arguments
	strArgs = ""
	If objArgs.Count >= 1 Then
		For i = 0 To objArgs.Count - 1 
			strArgs = strArgs & " " & objArgs(i)
		Next
	Else
		strArgs = " +menu 1 +fullscreen 1"
	End If
	
	' Run BF2 :)
	objShell.Run "BF2_w32ded.EXE" & strArgs, 1, True
	
	' Remove Outsmart Permissions
	objShell.Run "SetACL.exe -on ""%SystemRoot%\System32\drivers\etc\HOSTS""  -ot file -actn clear -clr dacl", 1, True
		
	' Re-write HOSTS file back to original
	Set objTS = objFS.OpenTextFile(strHostsFile ,FOR_WRITING)
	For i = 0 To UBound(arrLines) - 1
		objTS.WriteLine arrLines(i)
	Next
	objTS.Close

Else
	strErrMsg = "ERROR: Could not resolve '" & strLookupAddr & "'!" & vbCrLf
	strErrMsg = strErrMsg & "Please Manually update your HOSTS file!"
	Wscript.Echo strErrMsg
End If
' ========================================================================
