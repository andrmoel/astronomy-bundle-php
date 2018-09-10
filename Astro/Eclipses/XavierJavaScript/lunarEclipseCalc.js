<!--
//<![CDATA[
// Lunar Eclipse Calculator for Google Maps v3 (Xavier Jubier: http://xjubier.free.fr/)
// Copyright (C) 2007-2018 Xavier M. Jubier
//

/*
Javascript Lunar Eclipse Calculator for "FIVE MILLENNIUM CANON OF LUNAR ECLIPSES: -1999 TO +3000"
Copyright (C) 2007-2018 Xavier M. Jubier

Modifications:
2007-01-30   Xavier Jubier   Version for "FIVE MILLENNIUM CANON OF LUNAR ECLIPSES: -1999 TO +3000"
2010-06-31   Xavier Jubier   Added elevation profile at click location
2010-07-01   Xavier Jubier   Improved HTML5 Canvas support
*/

var strUserAgent = navigator.userAgent.toLowerCase();
var isSafari = ( strUserAgent.indexOf("safari") != -1 ) && ( strUserAgent.indexOf("chrome") == -1 );
var isFirefox = ( strUserAgent.indexOf("mozilla") != -1 ) && ( strUserAgent.indexOf("firefox") != -1 );
var isIE = ( strUserAgent.indexOf("msie") != -1 ) && ( strUserAgent.indexOf("opera") == -1 );	// Use <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> for IE8 VML compatibility
var isWin = ( strUserAgent.indexOf("windows") != -1 );
var gSVG_VML_Support = 0;
var D2R = Math.PI / 180.0;
var R2D = 180.0 / Math.PI;
var gIFRAMEindex = 0; // Used to workaround an iframe Firefox long-standing bug
var gIFRAMEid = ""; // Used to workaround an iframe Firefox long-standing bug
var gNbLoadiframe = 0;
var gRefractionHeight = -0.00524;	// Take Moon radius into account
//var gRefractionHeight = -0.01454;	// Take refraction into account
var kEARTH_EQUATORIAL_RADIUS = 6378137.0;

/*if ( !document.namespaces["g_vml_"] )
  document.namespaces.add("g_vml_", "urn:schemas-microsoft-com:vml");
document.namespaces.add("v", "urn:schemas-microsoft-com:vml", "#default#VML");
var ss = document.createStyleSheet();
ss.addRule("v\\:shape", "behavior: url(#default#VML); display: inline-block; antialias: true;");
ss.addRule("v\\:group", "behavior: url(#default#VML);");
ss.addRule("v\\:line", "behavior: url(#default#VML);");
ss.addRule("v\\:polyline", "behavior: url(#default#VML);");
ss.addRule("v\\:stroke", "behavior: url(#default#VML);");
ss.addRule("v\\:fill", "behavior: url(#default#VML);");
ss.addRule("v\\:rect", "behavior: url(#default#VML);");
ss.addRule("v\\:oval", "behavior: url(#default#VML);");*/
if (document.all && document.namespaces && !window.opera)
{
    if (document.namespaces["v"] != null)
    {
        document.namespaces.add("v", "urn:schemas-microsoft-com:vml", "#default#VML");
        var e = ["shape","shapetype","group","background","path","formulas","handles", "fill","stroke","shadow","textbox","textpath","imagedata","line","polyline", "curve","roundrect","oval","rect","arc","image"];
        var s = document.createStyleSheet();
        for(var i = 0; i < e.length; i++)
            s.addRule("v\\:" + e[i], "behavior: url(#default#VML);");
    }
}

//
// Observer constants -
// (0) North Latitude (radians)
// (1) West Longitude (radians)
// (2) Altitude (meters)
// (3) West time zone (hours)
//
// Note that correcting for refraction will involve creating a "virtual" altitude
// for each contact, and hence a different value of rho and O' for each contact!
//
var obsvconst = new Array();

//
// Eclipse circumstances
//  (0) Event type (P1=-3, U1=-2, U2=-1, Mid=0, U3=1, U4=2, P4=3)
//  (1) t
//  (2) hour angle
//  (3) declination
//  (4) altitude
//  (5) azimuth
//  (6) visibility (0 = above horizon, 1 = no event, 2 = below horizon)
//
var p1 = new Array();
var u1 = new Array();
var u2 = new Array();
var mid = new Array();
var u3 = new Array();
var u4 = new Array();
var p4 = new Array();
var moonrise = new Array();
var moonset = new Array();

/*
 * elements
 * (0)
 * (1)
 * (2) deltaT
 * (3)
 * (4)
 * (5) eclipse type (1 = total, 2 = partial, ????)
 * (6) hour angle (?)
 * (7) altitude (?)
 * (8) visiblity (?)
 * (9) t (p1)
 * (10) t (u1)
 * (11) t (u2)
 * (12) t (mid)
 * (13) t (u3)
 * (14) t (u4)
 * (15) t (p4)
 * (16) right ascension (0)
 * (17) right ascension (1)
 * (18) right ascension (2)
 * (19) declination (0)
 * (20) declination (1)
 * (21) declination (2)
 */
//
// Populate the circumstances array (entry condition - circumstances[1] must contain the correct value)
function populatecircumstances(circumstances)
{
    var t, ra, dec, h, alt, tmp;

    t = circumstances[1];
    // Right ascension
    ra = (elements[18] * t) + elements[17];
    ra = (ra * t) + elements[16];
    // Declination
    dec = (elements[21] * t) + elements[20];
    dec = (dec * t) + elements[19];
    dec *= D2R;
    circumstances[3] = dec;
    // Hour angle
    h = 15.0 * (elements[6] + ((t - (elements[2] / 3600.0)) * 1.002737909350795)) - ra;
    h = (h * D2R) - obsvconst[1];
    circumstances[2] = h;
    // alt
    alt = Math.asin((Math.sin(obsvconst[0]) * Math.sin(dec)) + (Math.cos(obsvconst[0]) * Math.cos(dec) * Math.cos(h)));
    alt -= Math.asin(Math.sin(elements[7] * D2R) * Math.cos(alt));
    circumstances[4] = alt;
    // azi
    tmp = (Math.sin(dec) * Math.cos(obsvconst[0])) - (Math.cos(dec) * Math.cos(h) * Math.sin(obsvconst[0]));
    circumstances[5] = Math.atan(-Math.cos(dec) * Math.sin(h) / tmp);
    if (tmp < 0.0)
        circumstances[5] += Math.PI;
//  circumstances[5] = Math.atan2(-Math.cos(dec) * Math.sin(h), (Math.sin(dec) * Math.cos(obsvconst[0])) - (Math.cos(dec) * Math.cos(h) * Math.sin(obsvconst[0])));
    // Visibility
    if ((circumstances[4] * R2D) < -(elements[8] + 0.5667))
        circumstances[6] = 2;
    /*  else if (circumstances[4] < 0.0)
      {
        circumstances[4] = 0.0;
        circumstances[5] = 0.0;
        circumstances[6] = 2;
      }*/
    else
        circumstances[6] = 0;
}

//
// Populate the p1, u1, u2, mid, u3, u4 and p4 arrays
function getall( )
{
    p1[1] = elements[9];
    populatecircumstances(p1);
    mid[1] = elements[12];
    populatecircumstances(mid);
    p4[1] = elements[15];
    populatecircumstances(p4);
    if (elements[5] < 3) // Partial or total eclipse
    {
        u1[1] = elements[10];
        populatecircumstances(u1);
        u4[1] = elements[14];
        populatecircumstances(u4);
        if (elements[5] < 2) // Total eclipse
        {
            u2[1] = elements[11];
            u3[1] = elements[13];
            populatecircumstances(u2);
            populatecircumstances(u3);
        }
        else // Partial eclipse
        {
            u2[6] = 1;
            u3[6] = 1;
        }
    }
    else // Penumbral eclipse
    {
        u1[6] = 1;
        u2[6] = 1;
        u3[6] = 1;
        u4[6] = 1;
    }

    if ((p1[6] != 0) && (u1[6] != 0) && (u2[6] != 0) && (mid[6] != 0) && (u3[6] != 0) && (u4[6] != 0) && (p4[6] != 0))
        mid[6] = 1;
}

//
// Read the data, and populate the obsvconst array
function readdata( lat, lon, elv )
{
    var tmp;

    // Get the latitude
    obsvconst[0] = lat * D2R;

    // Get the longitude
    obsvconst[1] = -lon * D2R;

    var Elv = 0.0;
    if (typeof window.TZ !== "undefined")
        var TZ = window.TZ;
    else
    {
        window.TZ = 0.0;
        var TZ = 0.0;
    }
    if ((elv == -1.0) || (elv == 0.0))
    {
        if (location.search.length > 1)
        {
            var argstr = location.search.substring(1, location.search.length);
            var args = argstr.split("&");
            for (var i = 0; i < args.length; i++)
            {
                if ((args[i].substring(0, 4) == "Elv=") && (gElevationActive == false))
                    eval(unescape(args[i]));
                else if ((args[i].substring(0, 3) == "TZ=") && (gTimeZoneActive == false))
                    eval(unescape(args[i]));
            }
        }
    }
    else
        Elv = elv + 0.0;

    // Get the altitude (sea level by default)
    obsvconst[2] = Elv;

    // Get the time zone (UT by default)
    if ((TZ >= -12.0) && (TZ <= 14.0))
        obsvconst[3] = -TZ;	// Negative east, positive west
    else
        obsvconst[3] = 0.0;
}

//
// Read the data for the geolocation, and populate the obsvconst array
function readdata_geo( lat, lon, elv )
{
    var tmp;

    // Get the latitude
    obsvconst[0] = lat * D2R;

    // Get the longitude
    obsvconst[1] = -lon * D2R;

    // Get the altitude (sea level by default)
    obsvconst[2] = elv;

    // Get the time zone (UT by default)
    obsvconst[3] = 0.0;
}

//
// Read the deltaT value for the selected eclipse
function getdTValue( )
{
    var deltaT = elements[2];

    return deltaT.toFixed(1);
}

//
// Get the local date of an event (see AA p.63 or http://aa.usno.navy.mil/js/JulianDate.js)
function getdate( circumstances, language )
{
    var t, ans, jd, a, b, c, d, e, year, sign;

    // Calculate the JD for noon (TDT) the day before the day that contains T0
    jd = Math.floor(elements[0] - (elements[1] / 24.0));
    // Calculate the Local time (ie the offset in hours since midnight TDT on the day containing T0) to the nearest 0.1 sec
    t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[2] - 0.05) / 3600.0);
    if (t < 0.0)
        jd--;
    else if (t >= 24.0)
        jd++;
    if (jd >= 2299160.5)
    {
        a = Math.floor((jd - 1867216.25) / 36524.25);
        a += jd + 1.0 - Math.floor(a / 4.0);
    }
    else
        a = jd;
    b = a + 1525.0;
    c = Math.floor((b - 122.1) / 365.25);
    d = Math.floor(365.25 * c);
    e = Math.floor((b - d) / 30.6001);
    d = b - d - Math.floor(30.6001 * e);
    if (e < 13.5)
        e -= 1;
    else
        e -= 13;
    if (e > 2.5)
        year = c - 4716;
    else
        year = c - 4715;
    if (year >= 0)
        sign = 1;
    else
        sign = -1;
    year = Math.abs(year);
    if (year < 10)
        year = "000" + year;
    else if (year < 100)
        year = "00" + year;
    else if (year < 1000)
        year = "0" + year;
    if (sign == -1)
        year = "-" + year;
    if ( language == "fr" )
    {
        if (d < 10)
            ans = "0";
        else
            ans = "";
        ans += d + "/";
        if (e < 10)
            ans += "0";
        ans += e + "/";
        ans += year;
    }
    else
    {
        ans = year + "/";
        if (e < 10)
            ans += "0";
        ans += e + "/";
        if (d < 10)
            ans += "0";
        ans += d;
    }

    return ans;
}

//
// Get the UNIX timestamp of an event (see AA p.63 or http://aa.usno.navy.mil/js/JulianDate.js)
function getUTCTimestamp( circumstances )
{
    var jd, t, ans, a, b, c, d, e, year, sign;

    // JD for noon (TDT) the day before the day that contains T0
    jd = Math.floor(elements[0] - (elements[1] / 24.0));
    // Calculate the Local time (ie the offset in hours since midnight TDT on the day containing T0) to the nearest 0.1 sec
    t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[2] - 0.05) / 3600.0);
    if (t < 0.0)
        jd--;
    else if (t >= 24.0)
        jd++;
    if (jd >= 2299160.5)
    {
        a = Math.floor((jd - 1867216.25) / 36524.25);
        a += jd + 1.0 - Math.floor(a / 4.0);
    }
    else
        a = jd;
    b = a + 1525.0;
    c = Math.floor((b - 122.1) / 365.25);
    d = Math.floor(365.25 * c);
    e = Math.floor((b - d) / 30.6001);
    d = b - d - Math.floor(30.6001 * e);
    if (e < 13.5)
        e -= 1;
    else
        e -= 13;
    if (e > 2.5)
        year = c - 4716;
    else
        year = c - 4715;
    if (t < 0.0)
        t += 24.0;
    else if (t >= 24.0)
        t -= 24.0;
    var myDate = new Date(year, e - 1, d, Math.floor(t), Math.floor((t - Math.floor(t)) * 60.0), 0);

    return Math.round((myDate.getTime() / 1000) - (myDate.getTimezoneOffset() * 60));
}

//
// Get the local time of an event
function gettime( circumstances, language )
{
    var ans = "";
    var t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[2] - 0.05) / 3600.0);
    if (t < 0.0)
        t += 24.0;
    else if (t >= 24.0)
        t -= 24.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t) + ":";
    t = (t - Math.floor(t)) * 60.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t) + ":";
    t = (t - Math.floor(t)) * 60.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t);
    // Add an asterix if the altitude is less than zero
    if (circumstances[6] == 2) // Below the horizon
        ans += "*";

    return ans;
}

//
// Get the local time of an event
function gettimeshort( circumstances )
{
    var t, ans;

    ans = "";
    t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[2] - 0.05) / 3600.0);
    if (t < 0.0)
        t += 24.0;
    else if (t >= 24.0)
        t -= 24.0;
    t += 1.0 / 60.0;	// Round to the nearest minute
    if (t >= 24.0)
        t -= 24.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t) + ":";
    t = (t - Math.floor(t)) * 60.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t);

    return ans;
}

//
// Get the shorten local time of an event with integral seconds
function gettimemiddle( circumstances )
{
    var ans = "";
    var t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[2] - 0.05) / 3600.0);
    if (t < 0.0)
        t += 24.0;
    else if (t >= 24.0)
        t -= 24.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t) + ":";
    t = (t - Math.floor(t)) * 60.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t) + ":";
    t = (t - Math.floor(t)) * 60.0;
    if (t < 10.0)
        ans += "0";
    ans += t.toFixed(0);
    // Add an asterix if the altitude is less than zero
    if (circumstances[6] == 2) // Below the horizon
        ans += "*";

    return ans;
}

//
// Get the UT date and time of an event (used for the day and night visualisation)
function getUTdatetime( circumstances, numDate )
{
    var jd, t, a, b, c, d, e;

    if (circumstances)
    {
        // JD for noon (TDT) the day before the day that contains T0
        jd = Math.floor(elements[0] - (elements[1] / 24.0));
        // UT time (ie the offset in hours since midnight TDT on the day containing T0) to the nearest 0.1 sec
        t = circumstances[1] + elements[1] - ((elements[2] - 0.05) / 3600.0);
        if (t < 0.0)
        {
            t += 24.0;
            jd--;
        }
        else if (t >= 24.0)
        {
            t -= 24.0;
            jd++;
        }
        if (jd >= 2299160.5)
        {
            a = Math.floor((jd - 1867216.25) / 36524.25);
            a += jd + 1.0 - Math.floor(a / 4.0);
        }
        else
            a = jd;
        b = a + 1525.0;
        c = Math.floor((b - 122.1) / 365.25);
        d = Math.floor(365.25 * c);
        e = Math.floor((b - d) / 30.6001);
        d = b - d - Math.floor(30.6001 * e);
        if (e < 13.5)
            e -= 1;
        else
            e -= 13;
        if (e > 2.5)
            numDate.year = c - 4716;
        else
            numDate.year = c - 4715;
    }
    else
    {
        jd = elements[0] - (elements[2] / 86400.0) + 0.5;
        var z = Math.floor(jd);
        var f = jd - z;
        t = 12.0 + (24.0 * ((elements[0] - (elements[2] / 86400.0)) - Math.floor(elements[0] - (elements[2] / 86400.0))));
        if (t < 0.0)
            t += 24.0;
        else if (t >= 24.0)
            t -= 24.0;
        if (z >= 2299161.0)
        {
            a = Math.floor((z - 1867216.25) / 36524.25);
            a += z + 1.0 - Math.floor(a / 4.0);
        }
        else
            a = z;
        b = a + 1524.0;
        c = Math.floor((b - 122.1) / 365.25);
        d = Math.floor(365.25 * c);
        e = Math.floor((b - d) / 30.6001);
        d = b - d - Math.floor(30.6001 * e) + f;
        if (e < 14.0)
            e -= 1;
        else
            e -= 13;
        if (e > 2.0)
            numDate.year = c - 4716;
        else
            numDate.year = c - 4715;
    }
    numDate.month = e;
    numDate.day = Math.floor(d);
    numDate.hour = Math.floor(t);
    t = (t - Math.floor(t)) * 60.0;
    numDate.minute = Math.floor(t);
    t = (t - Math.floor(t)) * 60.0;
    numDate.second = Math.floor(t);
    numDate.millisecond = Math.floor(1000.0 * (t - Math.floor(t)));
}

//
// Get the altitude
function getalt( circumstances, language )
{
    var t, ans;

    t = (circumstances[4] * R2D) + 0.05;
    if (t < 0.0)
    {
        ans = "-";
        t = -t;
    }
    else
        ans = "+";
    var tmp = Math.floor(t);
    if (tmp < 10.0)
        ans += "0";
    ans += tmp;
    if ( language == "fr" )
        ans += ",";
    else
        ans += ".";
    ans += Math.floor(10.0 * (t - tmp));

    return ans;
}

//
// Get the azimuth
function getazi( circumstances, language )
{
    var ans = "";
    var t = circumstances[5] * R2D;
    if (t < 0.0)
        t += 360.0;
    else if (t >= 360.0)
        t -= 360.0;
    t += 0.05;
    if (t >= 360.0)
        t -= 360.0;
    var tmp = Math.floor(t);
    if (tmp < 100.0)
        ans += "0";
    if (tmp < 10.0)
        ans += "0";
    ans += tmp;
    if ( language == "fr" )
        ans += ",";
    else
        ans += ".";
    ans += Math.floor(10.0 * (t - tmp));

    return ans;
}

//
// Get the Peakfinder parameters
function getparams( circumstances, language )
{
    var ans, t, jd, a, b, c, d, e, year;

    ans = "?lat=" + (obsvconst[0] * R2D).toFixed(4);
    ans += "&lng=" + (-obsvconst[1] * R2D).toFixed(4);
    ans += "&off=1&azi=";
    t = circumstances[5] * R2D;
    if (t < 0.0)
        t += 360.0;
    else if (t >= 360.0)
        t -= 360.0;
    ans += t.toFixed(0);
    ans += "&zoom=4&cfg=rm&date=";
    // UT date
    jd = Math.floor(elements[0] - (elements[1] / 24.0));
    t = circumstances[1] + elements[1] - ((elements[2] - 0.05) / 3600.0);
    if (t < 0.0)
        jd--;
    else if (t >= 24.0)
        jd++;
    if (jd >= 2299160.5)
    {
        a = Math.floor((jd - 1867216.25) / 36524.25);
        a += jd + 1.0 - Math.floor(a / 4.0);
    }
    else
        a = jd;
    b = a + 1525.0;
    c = Math.floor((b - 122.1) / 365.25);
    d = Math.floor(365.25 * c);
    e = Math.floor((b - d) / 30.6001);
    d = b - d - Math.floor(30.6001 * e);
    if (e < 13.5)
        e -= 1;
    else
        e -= 13;
    if (e > 2.5)
        year = c - 4716;
    else
        year = c - 4715;
    ans += year + "-";
    if (e < 10)
        ans += "0";
    ans += e + "-";
    if (d < 10)
        ans += "0";
    ans += d;
    ans += "T";
    // UT time to the nearest minute
    t = circumstances[1] + elements[1] - ((elements[2] - 0.05) / 3600.0);
    if (t < 0.0)
        t += 24.0;
    else if (t >= 24.0)
        t -= 24.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t) + ":";
    t = (t - Math.floor(t)) * 60.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.round(t) + "Z";
    if ((circumstances[4] * R2D) < 20.0)
    {
        ans += "&binoazi=";
        t = circumstances[5] * R2D;
        if (t < 0.0)
            t += 360.0;
        else if (t >= 360.0)
            t -= 360.0;
        ans += t.toFixed(2);
        ans += "&binoalt=";
        t = circumstances[4] * R2D;
        ans += t.toFixed(2);
    }

    return ans;
}

//
// Get the Peakfinder title
function gettitle( circumstances, language )
{
    var ans = "";

    switch ( circumstances )
    {
        case moonrise:
            if ( language == "fr" )
                ans = "Panoramique du terrain au lever de Lune...";
            else
                ans = "Terrain panorama at moonrise...";
            break;
        case p1:
            if ( language == "fr" )
                ans = "Panoramique du terrain au premier contact avec la p&eacute;nombre...";
            else
                ans = "Terrain panorama at first penumbral contact...";
            break;
        case u1:
            if ( language == "fr" )
                ans = "Panoramique du terrain au premier contact avec l&rsquo;ombre...";
            else
                ans = "Terrain panorama at first umbral contact...";
            break;
        case u2:
            if ( language == "fr" )
                ans = "Panoramique du terrain au d&eacute;but de la totalit&eacute;...";
            else
                ans = "Terrain panorama at the beginning of totality...";
            break;
        case mid:
            if ( language == "fr" )
                ans = "Panoramique du terrain au maximum de l&rsquo;&eacute;clipse...";
            else
                ans = "Terrain panorama at maximum eclipse...";
            break;
        case u3:
            if ( language == "fr" )
                ans = "Panoramique du terrain &agrave; la fin de la totalit&eacute;...";
            else
                ans = "Terrain panorama at the end of totality...";
            break;
        case u4:
            if ( language == "fr" )
                ans = "Panoramique du terrain au dernier contact avec l&rsquo;ombre...";
            else
                ans = "Terrain panorama at last umbral contact...";
            break;
        case p4:
            if ( language == "fr" )
                ans = "Panoramique du terrain au dernier contact avec la p&eacute;nombre...";
            else
                ans = "Terrain panorama at last penumbral contact...";
            break;
        case moonset:
            if ( language == "fr" )
                ans = "Panoramique du terrain au coucher de Lune...";
            else
                ans = "Terrain panorama at moonset...";
            break;
    }

    return ans;
}

//
// Display the information about 1st contact with penumbra
function displayp1( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'P1\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'D&eacute;but de l&rsquo;&eacute;clipse par la p&eacute;nombre (<a href="http://www.peakfinder.org/' + getparams(p1, language) + '" class="watts" target="_blank" title="' + gettitle(p1, language) + '">P1</a>)';
    else
        html += 'Start of penumbral eclipse (<a href="http://www.peakfinder.org/' + getparams(p1, language) + '" class="watts" target="_blank" title="' + gettitle(p1, language) + '">P1</a>)';
    var alt = p1[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(p1, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(p1, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(p1, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(p1, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about 1st contact with umbra
function displayu1( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'U1\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'D&eacute;but de l&rsquo;&eacute;clipse partielle (<a href="http://www.peakfinder.org/' + getparams(u1, language) + '" class="watts" target="_blank" title="' + gettitle(u1, language) + '">O1</a>)';
    else
        html += 'Start of partial eclipse (<a href="http://www.peakfinder.org/' + getparams(u1, language) + '" class="watts" target="_blank" title="' + gettitle(u1, language) + '">U1</a>)';
    var alt = u1[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(u1, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(u1, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(u1, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(u1, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about 2nd contact with umbra
function displayu2( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'U2\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'D&eacute;but de l&rsquo;&eacute;clipse totale (<a href="http://www.peakfinder.org/' + getparams(u2, language) + '" class="watts" target="_blank" title="' + gettitle(u2, language) + '">T1</a>)';
    else
        html += 'Start of total eclipse (<a href="http://www.peakfinder.org/' + getparams(u2, language) + '" class="watts" target="_blank" title="' + gettitle(u2, language) + '">U2</a>)';
    var alt = u2[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(u2, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(u2, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(u2, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(u2, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about maximum eclipse
function displaymid( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'mid\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'Maximum&nbsp;de&nbsp;l&rsquo;&eacute;clipse';
    else
        html += 'Maximum&nbsp;eclipse';
    var alt = mid[4] * R2D;
    var true_alt = elevationRefraction(alt);
    gMaximumEclipseAltitude = true_alt;
    gMaximumEclipseAzimuth = getazi(mid, "en");
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(<a href="http://www.peakfinder.org/' + getparams(mid, language) + '" class="watts" target="_blank" title="' + gettitle(mid, language) + '">MAX</a>)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(mid, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(mid, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(mid, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(mid, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about 3rd contact with umbra
function displayu3( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'U3\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'Fin de l&rsquo;&eacute;clipse totale (<a href="http://www.peakfinder.org/' + getparams(u3, language) + '" class="watts" target="_blank" title="' + gettitle(u3, language) + '">T2</a>)';
    else
        html += 'End of total eclipse (<a href="http://www.peakfinder.org/' + getparams(u3, language) + '" class="watts" target="_blank" title="' + gettitle(u3, language) + '">U3</a>)';
    var alt = u3[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(u3, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(u3, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(u3, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(u3, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about 4th contact with umbra
function displayu4( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'U4\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'Fin de l&rsquo;&eacute;clipse partielle (<a href="http://www.peakfinder.org/' + getparams(u4, language) + '" class="watts" target="_blank" title="' + gettitle(u4, language) + '">O2</a>)';
    else
        html += 'End of partial eclipse (<a href="http://www.peakfinder.org/' + getparams(u4, language) + '" class="watts" target="_blank" title="' + gettitle(u4, language) + '">U4</a>)';
    var alt = u4[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(u4, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(u4, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(u4, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(u4, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about 4th contact with penumbra
function displayp4( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'P4\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'Fin de l&rsquo;&eacute;clipse par la p&eacute;nombre (<a href="http://www.peakfinder.org/' + getparams(p4, language) + '" class="watts" target="_blank" title="' + gettitle(p4, language) + '">P2</a>)';
    else
        html += 'End of penumbral eclipse (<a href="http://www.peakfinder.org/' + getparams(p4, language) + '" class="watts" target="_blank" title="' + gettitle(p4, language) + '">P4</a>)';
    var alt = p4[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(p4, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(p4, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(p4, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(p4, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about moonrise
function displaymoonrise( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'Moonrise\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'Lever de lune';
    else
        html += 'Moonrise';
    var alt = moonrise[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(<a href="http://www.peakfinder.org/' + getparams(moonrise, language) + '" class="watts" target="_blank" title="' + gettitle(moonrise, language) + '">RISE</a>)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(moonrise, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettimeshort(moonrise, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(moonrise, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(moonrise, language) + '&deg;</td></tr>';

    return html;
}

//
// Display the information about moonset
function displaymoonset( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'Moonset\', false);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += 'Coucher de lune';
    else
        html += 'Moonset';
    var alt = moonset[4] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(<a href="http://www.peakfinder.org/' + getparams(moonset, language) + '" class="watts" target="_blank" title="' + gettitle(moonset, language) + '">SET</a>)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? "EclipseRight" : "EclipseLeft") + '" nowrap="nowrap">' + getdate(moonset, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettimeshort(moonset, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(moonset, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(moonset, language) + '&deg;</td></tr>';

    return html;
}

//
// Get the horizon dip in degrees (apparent horizon altitude from the astronomical horizon) from an elevation in meters
function getHorizonDip( alt )
{
    var horizonDip;

    if (alt >= 0.0)
        horizonDip = Math.acos(kEARTH_EQUATORIAL_RADIUS / (kEARTH_EQUATORIAL_RADIUS + alt)) * R2D;
    else
        horizonDip = -Math.acos(kEARTH_EQUATORIAL_RADIUS / (kEARTH_EQUATORIAL_RADIUS - alt)) * R2D;

    return horizonDip;
}

//
// Re-calculate
function recalculate( language, lat, lon, elv, type )
{
    var html = "";
    var htmlp1 = "";
    var htmlu1 = "";
    var htmlu2 = "";
    var htmlmid = "";
    var htmlu3 = "";
    var htmlu4 = "";
    var htmlp4 = "";
    var htmlEclipse = "";
    var htmlmoonrise = "";
    var htmlmoonset = "";
    var isEclipse = true;
    gNoEclipse = false;

    if ( ( isNaN(lat) ) || ( isNaN(lon) ) )
        return html;
    if ( elv < 0.0 )
        elv = 0.0;

    readdata(lat, lon, elv);
    getall();
    deltaT = getdTValue();

console.log(mid);
    if (mid[6] != 1) // Is there an event?
    {
        if ((p1[6] == 0) || (p4[6] == 0))
        {
            htmlp1 = displayp1(language);
            htmlp4 = displayp4(language);
        }
        if ((u1[6] == 0) || (u4[6] == 0))
        {
            htmlu1 = displayu1(language);
            htmlu4 = displayu4(language);
        }
        if ((u2[6] == 0) || (u3[6] == 0))
        {
            htmlu2 = displayu2(language);
            htmlu3 = displayu3(language);
        }
        if (mid[6] != 1)
            htmlmid = displaymid(language);

        if ((u3[6] != 1) || (u4[6] != 1))
        {
            if ((u2[6] == 0) || (u3[6] == 0))
            {
                if ( language == "fr" )
                    htmlEclipse += "(\xE9clipse totale de Lune)";
                else
                    htmlEclipse += "(total lunar eclipse)";
            }
            else
            {
                if ((u1[6] == 0) || (u4[6] == 0))
                {
                    if ( language == "fr" )
                        htmlEclipse += "(\xE9clipse partielle de Lune)";
                    else
                        htmlEclipse += "(partial lunar eclipse)";
                }
                else
                {
                    if ( language == "fr" )
                        htmlEclipse += "(\xE9clipse de Lune par la p\xE9nombre)";
                    else
                        htmlEclipse += "(penumbral lunar eclipse)";
                }
            }
        }
    }
    else // ... or is there no event at all?
    {
        isEclipse = false;
        gNoEclipse = true;
        if ( language == "fr" )
            htmlEclipse += "(AUCUNE ECLIPSE DE LUNE)";
        else
            htmlEclipse += "(NO LUNAR ECLIPSE)";
    }

    if ( isEclipse == true )
    {
        html = '<div style="width: ' + (( language == "fr" ) ? '440' : '430') + 'px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
        html += '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
        html += '<tr>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + dd2dms(lat, 1, language) + '</td>';
        html += '<td align="center" class="EclipseCenter" nowrap="nowrap">&nbsp;&nbsp;&lt;&mdash;&gt;&nbsp;&nbsp;</td>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + (( language == "fr" ) ? lat.toFixed(5).replace(/\./, ',') : lat.toFixed(5)) + '&deg;</td>';
        html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter">&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter" nowrap="nowrap">';
        html += htmlEclipse;
        html += '</td>';
        html += '<td rowspan="2" align="center" class="EclipseCenter">&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        if ( language == "fr" )
            html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter"><a href="javascript:openGMWindow(\'xLE_GoogleMap3_Help.html\',\'\');" class="Index" title="Aide &laquo;Cartographie Google&raquo;">Aide</a></td>';
        else
            html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter"><a href="javascript:openGMWindow(\'xLE_GoogleMap3_Help.html\',\'\');" class="Index" title="&quot;Google Map&quot; Help">Help</a></td>';
        html += '</tr>';
        html += '<tr>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + dd2dms(lon, 2, language) + '</td>';
        html += '<td align="center" class="EclipseCenter" nowrap="nowrap">&nbsp;&nbsp;&lt;&mdash;&gt;&nbsp;&nbsp;</td>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + (( language == "fr" ) ? lon.toFixed(5).replace(/\./, ',') : lon.toFixed(5)) + '&deg;</td>';
        html += '</tr>';
        if ( obsvconst[2] != 0.0 )
        {
            html += '<tr>';
            if (obsvconst[2] < 9000.0)
                html += '<td colspan="3" align="center" class="EclipseCenter" nowrap="nowrap">' + (( language == "fr" ) ? (obsvconst[2]).toFixed(1).replace(/\./, ',') : (obsvconst[2]).toFixed(1)) + 'm (' + ((obsvconst[2] * 3.2808399).toFixed(0)) + 'ft)</td>';
            else	// From an aircraft
                html += '<td colspan="3" align="center" class="EclipseCenter" nowrap="nowrap">' + (( language == "fr" ) ? (obsvconst[2]).toFixed(1).replace(/\./, ',') : (obsvconst[2]).toFixed(1)) + 'm (' + ((obsvconst[2] * 3.2808399).toFixed(0)) + 'ft); ' + (( language == "fr" ) ? ('d&eacute;pression: ' + getHorizonDip(obsvconst[2]).toFixed(1).replace(/\./, ',')) : ('dip: ' + getHorizonDip(obsvconst[2]).toFixed(1))) + '&deg;</td>';
            html += '</tr>';
        }
        html += "</table>";

        html += '<div align="center" style="width: 430px; font-size: 7pt; font-weight: bold;"><center><table border="0" cellspacing="1" width="100%">';
        html += '<tr align="center" bgcolor="#DDAD08">';
        var TZ = (-obsvconst[3]).toFixed(1);
        if (obsvconst[3] < 0.0)
            TZ = "+" + TZ;
        if ( language == "fr" )
            html += '<th class="Eclipse" nowrap="nowrap">Phase&nbsp;(&Delta;T=' + deltaT.replace(/\./, ',') + 's)</th><th class="Eclipse">Date</th><th class="Eclipse" nowrap="nowrap">Heure&nbsp;(' + ((obsvconst[3] == 0.0) ? 'TU' : TZ.replace(/\./, ',')) + ')</th><th class="Eclipse">Alt</th><th class="Eclipse">Azi</th>';
        else
            html += '<th class="Eclipse" nowrap="nowrap">Event&nbsp;(&Delta;T=' + deltaT + 's)</th><th class="Eclipse">Date</th><th class="Eclipse" nowrap="nowrap">Time&nbsp;(' + ((obsvconst[3] == 0.0) ? 'UT' : TZ.replace(/\./, ',')) + ')</th><th class="Eclipse">Alt</th><th class="Eclipse">Azi</th>';
        html += '</tr>';

        // Look for moonrise/moonset during/around the eclipse
        getmoonrise(moonrise);
        if ( moonrise[1] != mid[1] )
            htmlmoonrise = displaymoonrise(language);
        getmoonset(moonset);
        if ( moonset[1] != mid[1] )
            htmlmoonset = displaymoonset(language);

        if ( ( htmlmoonrise != "" ) && ( p1[1] >= moonrise[1] ) )
            html += htmlmoonrise;
        html += htmlp1;
        if ( htmlu1 != "" )
        {
            if ( ( htmlmoonrise != "" ) && ( p1[1] < moonrise[1] ) && ( u1[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( p1[1] < moonset[1] ) && ( u1[1] >= moonset[1] ) )
                html += htmlmoonset;
            html += htmlu1;
        }
        else
        {
            if ( ( htmlmoonrise != "" ) && ( p1[1] < moonrise[1] ) && ( mid[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( p1[1] < moonset[1] ) && ( mid[1] >= moonset[1] ) )
                html += htmlmoonset;
        }
        if ( htmlu2 != "" )
        {
            if ( ( htmlmoonrise != "" ) && ( u1[1] < moonrise[1] ) && ( u2[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( u1[1] < moonset[1] ) && ( u2[1] >= moonset[1] ) )
                html += htmlmoonset;
            html += htmlu2;
            if ( ( htmlmoonrise != "" ) && ( u2[1] < moonrise[1] ) && ( mid[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( u2[1] < moonset[1] ) && ( mid[1] >= moonset[1] ) )
                html += htmlmoonset;
        }
        else
        {
            if ( ( htmlmoonrise != "" ) && ( u1[1] < moonrise[1] ) && ( u2[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( u1[1] < moonset[1] ) && ( u2[1] >= moonset[1] ) )
                html += htmlmoonset;
        }
        if ( htmlmid != "" )
            html += htmlmid;
        if ( htmlu3 != "" )
        {
            if ( ( htmlmoonrise != "" ) && ( mid[1] < moonrise[1] ) && ( u3[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( u3[1] > moonset[1] ) && ( mid[1] <= moonset[1] ) )
                html += htmlmoonset;
            html += htmlu3;
            if ( ( htmlmoonrise != "" ) && ( u3[1] < moonrise[1] ) && ( u4[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( u3[1] < moonset[1] ) && ( u4[1] >= moonset[1] ) )
                html += htmlmoonset;
        }
        else
        {
            if ( ( htmlmoonrise != "" ) && ( u3[1] < moonrise[1] ) && ( u4[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( u3[1] < moonset[1] ) && ( u4[1] >= moonset[1] ) )
                html += htmlmoonset;
        }
        if ( htmlu4 != "" )
        {
            html += htmlu4;
            if ( ( htmlmoonrise != "" ) && ( u4[1] < moonrise[1] ) && ( p4[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( u4[1] < moonset[1] ) && ( p4[1] >= moonset[1] ) )
                html += htmlmoonset;
        }
        else
        {
            if ( ( htmlmoonrise != "" ) && ( mid[1] < moonrise[1] ) && ( p4[1] >= moonrise[1] ) )
                html += htmlmoonrise;
            if ( ( htmlmoonset != "" ) && ( p4[1] > moonset[1] ) && ( mid[1] <= moonset[1] ) )
                html += htmlmoonset;
        }
        html += htmlp4;
        if ( ( htmlmoonset != "" ) && ( p4[1] <= moonset[1] ) )
            html += htmlmoonset;
        html += '</table></center></div>';

        if (typeof daynight !== "undefined")
        {
            var numDateDN = new Object();
            getUTdatetime(mid, numDateDN);
            daynight.setDate(new Date(numDateDN.year, numDateDN.month - 1, numDateDN.day, numDateDN.hour, numDateDN.minute, numDateDN.second, numDateDN.millisecond));	// At maximum eclipse by default
        }
    }
    else // No eclipse
    {
        html = '<div style="width: 200px; font-size: 7pt; font-weight: bold; text-align: center; background-color: #FDF3D0;">';
        html += '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
        html += '<tr>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + dd2dms(lat, 1, language) + '</td>';
        html += '<td align="center" class="EclipseCenter" nowrap="nowrap">&nbsp;&nbsp;&lt;&mdash;&gt;&nbsp;&nbsp;</td>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + (( language == "fr" ) ? lat.toFixed(5).replace(/\./, ',') : lat.toFixed(5)) + '&deg;</td>';
        html += '</tr>';
        html += '<tr>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + dd2dms(lon, 2, language) + '</td>';
        html += '<td align="center" class="EclipseCenter" nowrap="nowrap">&nbsp;&nbsp;&lt;&mdash;&gt;&nbsp;&nbsp;</td>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + (( language == "fr" ) ? lon.toFixed(5).replace(/\./, ',') : lon.toFixed(5)) + '&deg;</td>';
        html += '</tr>';
        if ( obsvconst[2] != 0.0 )
        {
            html += '<tr>';
            if (obsvconst[2] < 9000.0)
                html += '<td colspan="3" align="center" class="EclipseCenter" nowrap="nowrap">' + (( language == "fr" ) ? (obsvconst[2]).toFixed(1).replace(/\./, ',') : (obsvconst[2]).toFixed(1)) + 'm (' + ((obsvconst[2] * 3.2808399).toFixed(0)) + 'ft)</td>';
            else	// From an aircraft
                html += '<td colspan="3" align="center" class="EclipseCenter" nowrap="nowrap">' + (( language == "fr" ) ? (obsvconst[2]).toFixed(1).replace(/\./, ',') : (obsvconst[2]).toFixed(1)) + 'm (' + ((obsvconst[2] * 3.2808399).toFixed(0)) + 'ft); ' + (( language == "fr" ) ? ('d&eacute;pression: ' + getHorizonDip(obsvconst[2]).toFixed(1).replace(/\./, ',')) : ('dip: ' + getHorizonDip(obsvconst[2]).toFixed(1))) + '&deg;</td>';
            html += '</tr>';
        }
        html += '</table>';
        html += '<br /><p style="text-align: center;">' + htmlEclipse + '</p>';
    }
    html += '</div>';

    return html;
}

//
// Re-calculate for geolocation
function recal_geo( language, lat, lon, elv, speed, heading )
{
    var html = "";
    var htmlEclipse = "";
    var isEclipse = true;

    if ( ( isNaN(lat) ) || ( isNaN(lon) ) )
        return html;
    if ( elv < 0.0 )
        elv = 0.0;

    readdata_geo(lat, lon, elv);
    getall();
    deltaT = getdTValue();
    if (mid[6] != 1) // Is there an event?
    {
        if ((u3[6] != 1) || (u4[6] != 1))
        {
            if ((u2[6] == 0) || (u3[6] == 0))
            {
                if ( language == "fr" )
                    htmlEclipse += "Totale";
                else
                    htmlEclipse += "Total";
            }
            else
            {
                if ((u1[6] == 0) || (u4[6] == 0))
                {
                    if ( language == "fr" )
                        htmlEclipse += "Partielle";
                    else
                        htmlEclipse += "Partial";
                }
                else
                {
                    if ( language == "fr" )
                        htmlEclipse += "Par la p\xE9nombre";
                    else
                        htmlEclipse += "Penumbral";
                }
            }
        }
    }
    else // ... or is there no event at all?
    {
        isEclipse = false;
        if ( language == "fr" )
            htmlEclipse += "AUCUNE ECLIPSE DE LUNE VISIBLE";
        else
            htmlEclipse += "NO VISIBLE LUNAR ECLIPSE";
    }

    var navUA = navigator.userAgent.toLowerCase();
    var isPhoneTablet = ( (navUA.indexOf("iphone") != -1) || (navUA.indexOf("ipad") != -1) || (navUA.indexOf("ipod") != -1) || (navUA.indexOf("android") != -1) || (navUA.indexOf("bb10") != -1) || (navUA.indexOf("iemobile") != -1) || (navUA.indexOf("mobile") != -1) || (navUA.indexOf("tablet") != -1) );
    if ( isEclipse == true )
    {
        var true_alt = elevationRefraction(mid[4] * R2D);
        if ( language == "fr" )
        {
            htmlEclipse += "<br />Lune au max.:\xA0" + true_alt.toFixed(1).replace(/\./, ',') + "&deg; Azi.:\xA0" + getazi(mid, language) + "&deg;";
            htmlEclipse += "<br />Maximum:\xA0" + getdate(mid, language) + " &agrave; " + gettimemiddle(mid) + "TU";
        }
        else
        {
            htmlEclipse += "<br />Moon at max.:\xA0" + true_alt.toFixed(1) + "&deg; Az.:\xA0" + getazi(mid, language) + "&deg;";
            htmlEclipse += "<br />Maximum:\xA0" + getdate(mid, language) + " at " + gettimemiddle(mid) + "UT";
        }
//    html = '<div id="geolocationdata" style="font-size: ' + (( isPhoneTablet == false ) ? '7' : '6') + 'pt; font-weight: bold; text-align: left; letter-spacing: -0.5px; white-space: nowrap;" nowrap="nowrap">';
        html = '<div id="geolocationdata" style="font-size: ' + (( isPhoneTablet == false ) ? '7' : '6') + 'pt; font-weight: bold; text-align: left; white-space: nowrap;" nowrap="nowrap">';
        html += htmlEclipse;
        html += '</div>';
    }
    else // No eclipse
        html = '<p style="font-size: ' + (( isPhoneTablet == false ) ? '7' : '6') + 'pt; font-weight: bold; text-align: center; letter-spacing: -0.5px; white-space: nowrap;">' + htmlEclipse + '</p>';

    return html;
}

function eclipseCalcPreload( )
{
    return;
}

//
// The values given are for 10¡C and 100.3 kPa. Add 1% to the refraction for every 3¡C colder, subtract if hotter
// (hot air is less dense, and will therefore have less refraction). Add 1% for every 0.9 kPa higher pressure,
// subtract if lower.
// As the atmospheric refraction is 34' on the horizon itself, but only 29' above it, the setting or rising sun
// seems to be flattened by about 5' (about 1/6 of its apparent diameter).
function elevationRefraction( elv_geometric )
{
    if ( elv_geometric > 10.2 )
        refraction = 0.01617 * Math.cos(elv_geometric * D2R) / Math.sin(elv_geometric * D2R);
    else
    {
        var a0 = 0.58804392;
        var a1 = -0.17941557;
        var a2 = 0.29906946e-1;
        var a3 = -0.25187400e-2;
        var a4 = 0.82622101e-4;
        var x = Math.abs(elv_geometric + 0.589);
        var x2 = x * x;
        var x3 = x * x2;
        var x4 = x2 * x2;
        refraction = Math.abs(a0 + (a1 * x) + (a2 * x2) + (a3 * x3) + (a4 * x4));
    }
    var elv_observed = elv_geometric + refraction;

    return(elv_observed);
}

function drawDiagram( event, inline )
{
    if (typeof daynight !== "undefined")
    {
        var numDateDN = new Object();
        switch ( event )
        {
            case "P1":
                getUTdatetime(p1, numDateDN);
                break;
            case "U1":
                getUTdatetime(u1, numDateDN);
                break;
            case "U2":
                getUTdatetime(u2, numDateDN);
                break;
            case "mid":
            default:
                getUTdatetime(mid, numDateDN);
                break;
            case "U3":
                getUTdatetime(u3, numDateDN);
                break;
            case "U4":
                getUTdatetime(u4, numDateDN);
                break;
            case "P4":
                getUTdatetime(p4, numDateDN);
                break;
            case "Moonrise":
                getUTdatetime(moonrise, numDateDN);
                break;
            case "Moonset":
                getUTdatetime(moonset, numDateDN);
                break;
        }
        daynight.setDate(new Date(numDateDN.year, numDateDN.month - 1, numDateDN.day, numDateDN.hour, numDateDN.minute, numDateDN.second, numDateDN.millisecond));	// At maximum eclipse by default
    }
}

//
// Get the moonrise circumstances
function getmoonrise( circumstances )
{
    var t, ans, alt, tmp;

    circumstances[0] = -3;
    circumstances[1] = p1[1] - 0.8;
    circumstances[4] = -1.0;
    t = circumstances[1];

    do
    {
        t += 1.0 / 180.0;	// Every 20 seconds

        // Right ascension
        ra = (elements[18] * t) + elements[17];
        ra = (ra * t) + elements[16];
        // Declination
        dec = (elements[21] * t) + elements[20];
        dec = (dec * t) + elements[19];
        dec *= D2R;
        circumstances[3] = dec;
        // Hour angle
        h = 15.0 * (elements[6] + ((t - (elements[2] / 3600.0)) * 1.002737909350795)) - ra;
        h = (h * D2R) - obsvconst[1];
        circumstances[2] = h;

        // alt
        alt = Math.asin((Math.sin(obsvconst[0]) * Math.sin(dec)) + (Math.cos(obsvconst[0]) * Math.cos(dec) * Math.cos(h)));
        alt -= Math.asin(Math.sin(elements[7] * D2R) * Math.cos(alt));
        circumstances[4] = alt;
    }
    while ( ( circumstances[4] < gRefractionHeight ) && ( Math.abs(t - mid[1]) < 4.0 ) );
    if ( ( circumstances[4] < 0.0 ) && ( Math.abs(t - mid[1]) < 4.0 ) )
    {
        circumstances[1] = t;
        circumstances[6] = 0;
    }
    else
    {
        circumstances[1] = mid[1];
        circumstances[6] = 1;
        return;
    }

    // azi
    tmp = (Math.sin(dec) * Math.cos(obsvconst[0])) - (Math.cos(dec) * Math.cos(h) * Math.sin(obsvconst[0]));
    circumstances[5] = Math.atan(-Math.cos(dec) * Math.sin(h) / tmp);
    if (tmp < 0.0)
        circumstances[5] += Math.PI;
//  circumstances[5] = Math.atan2(-Math.cos(dec) * Math.sin(h), (Math.sin(dec) * Math.cos(obsvconst[0])) - (Math.cos(dec) * Math.cos(h) * Math.sin(obsvconst[0])));
}

//
// Get the moonset circumstances
function getmoonset( circumstances )
{
    var t, ans, alt, tmp;

    circumstances[0] = 3;
    circumstances[1] = p4[1] + 0.8;
    circumstances[4] = -1.0;
    t = circumstances[1];

    do
    {
        t -= 1.0 / 180.0;	// Every 20 seconds

        // Right ascension
        ra = (elements[18] * t) + elements[17];
        ra = (ra * t) + elements[16];
        // Declination
        dec = (elements[21] * t) + elements[20];
        dec = (dec * t) + elements[19];
        dec *= D2R;
        circumstances[3] = dec;
        // Hour angle
        h = 15.0 * (elements[6] + ((t - (elements[2] / 3600.0)) * 1.002737909350795)) - ra;
        h = (h * D2R) - obsvconst[1];
        circumstances[2] = h;

        // alt
        alt = Math.asin((Math.sin(obsvconst[0]) * Math.sin(dec)) + (Math.cos(obsvconst[0]) * Math.cos(dec) * Math.cos(h)));
        alt -= Math.asin(Math.sin(elements[7] * D2R) * Math.cos(alt));
        circumstances[4] = alt;
    }
    while ( ( circumstances[4] < gRefractionHeight ) && ( Math.abs(t - mid[1]) < 4.0 ) );
    if ( ( circumstances[4] < 0.0 ) && ( Math.abs(t - mid[1]) < 4.0 ) )
    {
        circumstances[1] = t;
        circumstances[6] = 0;
    }
    else
    {
        circumstances[1] = mid[1];
        circumstances[6] = 1;
        return;
    }

    // azi
    tmp = (Math.sin(dec) * Math.cos(obsvconst[0])) - (Math.cos(dec) * Math.cos(h) * Math.sin(obsvconst[0]));
    circumstances[5] = Math.atan(-Math.cos(dec) * Math.sin(h) / tmp);
    if (tmp < 0.0)
        circumstances[5] += Math.PI;
//  circumstances[5] = Math.atan2(-Math.cos(dec) * Math.sin(h), (Math.sin(dec) * Math.cos(obsvconst[0])) - (Math.cos(dec) * Math.cos(h) * Math.sin(obsvconst[0])));
}
//]]>
//-->