<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="../twfyapi.cs" Src="../twfyapi.cs" Inherits="TWFYAPI.TWFYAPI" %>

<%
    // Set up a new instance of the API binding
    TWFYAPI.TWFYAPI twfyapi = new TWFYAPI.TWFYAPI("DpPSWnGj7XPRGePtfMGWvGqQ");
    
    // Constituency name
    String constituency = "Macclesfield";

    // Get the constituency's boundary map in KML format (via http://mapit.mysociety.org)
    String boundary = twfyapi.query("getBoundary", new String[] {"output:xml","name:" + constituency});

    // Serve the boundary map
    Response.ContentType = "application/vnd.google-earth.kml+xml; encoding=utf-8";
    Response.AppendHeader("Content-Disposition", 'attachment; filename="TheyWorkForYou " + constituency + " Constituency Boundary Map.kml"');
    Response.Write(boundary);
%>