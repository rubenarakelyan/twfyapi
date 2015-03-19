<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="../twfyapi.cs" Src="../twfyapi.cs" Inherits="TWFYAPI.TWFYAPI" %>

<%
    // Set up a new instance of the API binding
    TWFYAPI.TWFYAPI twfyapi = new TWFYAPI.TWFYAPI("DpPSWnGj7XPRGePtfMGWvGqQ");

    // Get a list of Labour MPs in XML format
    String mps = twfyapi.query("getMPs", new String[] {"output:xml","party:labour"});

    // Print out the list
    Response.ContentType = "application/xml";
    Response.Write(mps);
%>