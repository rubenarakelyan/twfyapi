<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="../oaapi.cs" Src="../oaapi.cs" Inherits="OAAPI.OAAPI" %>

<%
    // Set up a new instance of the API binding
    OAAPI.OAAPI oaapi = new OAAPI.OAAPI("ChDS2CGxMoPbGzY2taAuEktx");

    // Get a list of Labor representatives in XML format
    String reps = oaapi.query("getRepresentatives", new String[] {"output:xml","party:labor"});

    // Print out the list
    Response.ContentType = "application/xml";
    Response.Write(reps);
%>