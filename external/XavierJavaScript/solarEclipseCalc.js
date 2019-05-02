<!--
//<![CDATA[
// Solar Eclipse Calculator & Diagram for Google Maps v3 (Xavier Jubier: http://xjubier.free.fr/)
// Copyright (C) 2007-2017 Xavier M. Jubier
//

/*
 Javascript Solar Eclipse Calculator for "FIVE MILLENNIUM CANON OF SOLAR ECLIPSES: -1999 TO +3000"
 Copyright (C) 2007-2017 Xavier M. Jubier

 Modifications:
 2007-01-30   Xavier Jubier   Version for "FIVE MILLENNIUM CANON OF SOLAR ECLIPSES: -1999 TO +3000"
 2007-07-20   Xavier Jubier   Added eclipse diagram and XML
 2008-01-18   Xavier Jubier   Added altitude with refraction (no time correction)
 2008-03-12   Xavier Jubier   Minor corrections and huge code adaptation for most browsers
 2009-01-05   Xavier Jubier   Moon libration calculation for Watts chart
 2009-01-29   Xavier Jubier   Lunar limb corrections with Watts chart
 2009-11-20   Xavier Jubier   Lunar limb corrections with Kaguya's DEM
 2010-06-31   Xavier Jubier   Added elevation profile at click location
 2010-07-01   Xavier Jubier   Improved HTML5 Canvas support
 2013-01-31   Xavier Jubier   Solar mesosphere 0.5" correction (add &Mes=1 to URL)
 2013-03-26   Xavier Jubier   Fix for annulars near the limits
 2014-05-30   Xavier Jubier   Geolocation tracking (https://developer.mozilla.org/en-US/docs/Web/API/Geolocation/Using_geolocation)
 2016-01-03   Xavier Jubier   (Ant-)Umbral shadow outline at maximum eclipse, c1, c2, c3 and c4
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
var gRefractionHeight = -0.00524;	// Take Sun radius into account
//var gRefractionHeight = -0.01454;	// Take refraction into account
var gMoonData = new Object();
var gVSOP = 1;
var gMes = 0;
var gXMLRequest = false;
var kEARTH_EQUATORIAL_RADIUS = 6378137.0;
var kEARTH_POLAR_RADIUS = 6356752.314245;
var kELLIPTICITY_SQUARRED =  0.00669437999014;
var kEARTH_INV_FLATTENING = 0.00335281066475;
var kEARTH_INV_F_SQUARRED = 1.00673949674228;
var kEARTH_INV_F_SQ = 0.00673949674228;
var kMINOR_MAJOR_RADIUS_RATIO = 0.99664718933525;
var kLATITUDE_FLATTENING = 1.00336408982098;
var kSIDEREAL2SOLARTIME = 0.004178074622295;
var kSUN_RADIUS_DEG = 0.26667;			// Mean apparent solar radius (in degrees)
var kSHADOW_DEGREES_STEPSIZE = 2.0;
var kEPSILON_OUTLINE = 1.0e-8;
var kITERATION_OUTLINE = 10;			// Maximum number of iterations for the shadow outline
var kM_PI_d2 = Math.PI / 2.0;
var kM_PI_x2 = Math.PI * 2.0;
var gShadowOutline = null;
var gShadowOutlineCoords = new Array();
var gPolylineShadowOutline = null;

//
// Observer constants -
// (0) North Latitude (radians)
// (1) West Longitude (radians)
// (2) Altitude (meters)
// (3) West time zone (hours)
// (4) rho sin O'
// (5) rho cos O'
// (6) index into the elements array for the eclipse in question
//
// Note that correcting for refraction will involve creating a "virtual" altitude
// for each contact, and hence a different value of rho and O' for each contact!
//
var obsvconst = new Array();

//
// Eclipse circumstances
//  (0) Event type (C1=-2, C2=-1, Mid=0, C3=1, C4=2)
//  (1) t
// -- time-only dependent circumstances (and their per-hour derivatives) follow --
//  (2) x
//  (3) y
//  (4) d
//  (5) sin d
//  (6) cos d
//  (7) mu
//  (8) l1
//  (9) l2
// (10) dx
// (11) dy
// (12) dd
// (13) dmu
// (14) dl1
// (15) dl2
// -- time and location dependent circumstances follow --
// (16) h
// (17) sin h
// (18) cos h
// (19) xi
// (20) eta
// (21) zeta
// (22) dxi
// (23) deta
// (24) u
// (25) v
// (26) a
// (27) b
// (28) l1'
// (29) l2'
// (30) n^2
// -- observational circumstances follow --
// (31) p position angle measured from the north point of the Sun
// (32) alt
// (33) q parallactic angle
// (34) v position angle measured from the zenith point of the solar limb towards the west (Z/V)
// (35) azi
// (36) m (maximum eclipse and c1/c4 when available) or limb correction at c2/c3 (where available!)
// (37) magnitude (maximum eclipse and c1/c4 when available)
// (38) Moon/Sun ratio (maximum eclipse only)
// (39) calculated local event type for a transparent earth (0 = none, 1 = partial, 2 = annular, 3 = total)
// (40) event visibility (0 = above horizon, 1 = below horizon, 2 = sunrise, 3 = sunset, 4 = below horizon, disregard)
// (41) Moon altitude (diagram)
// (42) Moon azimuth (diagram)
// (43) Sun radius in degrees
// (44) Moon radius in degrees
// (45) Sun altitude (diagram)
// (46) Sun azimuth (diagram)
// (47) Moon distance in earth radii
// (48) Moon libration in longitude in degrees
// (49) Moon libration in latitude in degrees
// (50) Moon north pole axis in degrees
// (51) Sun north pole axis in degrees
//
var lambdak1k2 = 1.00076024401; // = k1/k2 with k1 = 0.2724880 and k2 = 0.2722810 or 1.00076026356 with 0.272481 and 0.272274
//var lambdak1k2 = 1.00083222847; // = k1/k2 with k1 = 0.2725076 and k2 = 0.2722810 (IAU 1982)
var f1, f2;

var c1 = new Array();
var c2 = new Array();
var mid = new Array();
var c3 = new Array();
var c4 = new Array();
var sunrise = new Array();
var sunset = new Array();

var c1_alt = new Array(2);
var c1_azi = new Array(2);
var c1_rad = new Array(2);
var c2_alt = new Array(2);
var c2_azi = new Array(2);
var c2_rad = new Array(2);
var mid_alt = new Array(2);
var mid_azi = new Array(2);
var mid_rad = new Array(2);
var c3_alt = new Array(2);
var c3_azi = new Array(2);
var c3_rad = new Array(2);
var c4_alt = new Array(2);
var c4_azi = new Array(2);
var c4_rad = new Array(2);
var V = new Array(2);
var PV = new Array(5);
var sunrise_alt = new Array(2);
var sunrise_azi = new Array(2);
var sunrise_rad = new Array(2);
var sunset_alt = new Array(2);
var sunset_azi = new Array(2);
var sunset_rad = new Array(2);

//
// Populate the circumstances array with the time-only dependent circumstances (x, y, d, m, ...)
function timedependent( circumstances )
{
    var t = circumstances[1];
    // x
    var ans = elements[9] * t + elements[8];
    ans = ans * t + elements[7];
    ans = ans * t + elements[6];
    circumstances[2] = ans;

    // dx
    ans = 3.0 * elements[9] * t + 2.0 * elements[8];
    ans = ans * t + elements[7];
    circumstances[10] = ans;

    // y
    ans = elements[13] * t + elements[12];
    ans = ans * t + elements[11];
    ans = ans * t + elements[10];
    circumstances[3] = ans;
    // dy
    ans = 3.0 * elements[13] * t + 2.0 * elements[12];
    ans = ans * t + elements[11];
    circumstances[11] = ans;

    // d
    ans = elements[16] * t + elements[15];
    ans = ans * t + elements[14];
    ans *= D2R;
    circumstances[4] = ans;
    // sin d and cos d
    circumstances[5] = Math.sin(ans);
    circumstances[6] = Math.cos(ans);
    // dd
    ans = 2.0 * elements[16] * t + elements[15];
    ans *= D2R;
    circumstances[12] = ans;
    // m
    ans = elements[19] * t + elements[18];
    ans = ans * t + elements[17];
    if (ans >= 360.0)
        ans -= 360.0;
    ans *= D2R;
    circumstances[7] = ans;
    // dm
    ans = 2.0 * elements[19] * t + elements[18];
    ans *= D2R;
    circumstances[13] = ans;
    // l1 and dl1
    ans = elements[22] * t + elements[21];
    ans = ans * t + elements[20];
    circumstances[8] = ans;
    var type = circumstances[0];
    if ((type == -2) || (type == 0) || (type == 2))
        circumstances[14] = 2.0 * elements[22] * t + elements[21];
    // l2 and dl2
    ans = elements[25] * t + elements[24];
    ans = ans * t + elements[23];
    circumstances[9] = ans;
    if ((type == -1) || (type == 0) || (type == 1))
        circumstances[15] = 2.0 * elements[25] * t + elements[24];

    return circumstances;
}

//
// Populate the circumstances array with the time and location dependent circumstances
function timelocdependent( circumstances )
{
    timedependent(circumstances);

    // h, sin h, cos h
    circumstances[16] = circumstances[7] - obsvconst[1] - (elements[5] / 13713.440924999626077);
    circumstances[17] = Math.sin(circumstances[16]);
    circumstances[18] = Math.cos(circumstances[16]);


    // xi
    circumstances[19] = obsvconst[5] * circumstances[17];
    // eta
    circumstances[20] = (obsvconst[4] * circumstances[6]) - (obsvconst[5] * circumstances[18] * circumstances[5]);


    // zeta
    circumstances[21] = (obsvconst[4] * circumstances[5]) + (obsvconst[5] * circumstances[18] * circumstances[6]);


    // dxi
    circumstances[22] = circumstances[13] * obsvconst[5] * circumstances[18];



    // deta
    circumstances[23] = (circumstances[13] * circumstances[19] * circumstances[5]) - (circumstances[21] * circumstances[12]);


    // u
    circumstances[24] = circumstances[2] - circumstances[19];
    // v
    circumstances[25] = circumstances[3] - circumstances[20];
    // a
    circumstances[26] = circumstances[10] - circumstances[22];
    // b
    circumstances[27] = circumstances[11] - circumstances[23];

    var type = circumstances[0];
    // l1'
    if ((type == -2) || (type == 0) || (type == 2))
        circumstances[28] = circumstances[8] - (circumstances[21] * elements[26]);

    // l2'
    if ((type == -1) || (type == 0) || (type == 1))
        circumstances[29] = circumstances[9] - (circumstances[21] * elements[27]);
    // n^2
    circumstances[30] = (circumstances[26] * circumstances[26]) + (circumstances[27] * circumstances[27]);


    return circumstances;
}

//
// Iterate on C1 or C4
function c1c4iterate( circumstances )
{
    var sign, n;

    timelocdependent(circumstances);
    if (circumstances[0] < 0)
        sign = -1.0;
    else
        sign = 1.0;
    var tau = 1.0;
    var iter = 0;
    while ((Math.abs(tau) > 0.000001) && (iter < 50))
    {
        n = Math.sqrt(circumstances[30]);
        tau = (circumstances[26] * circumstances[25]) - (circumstances[24] * circumstances[27]);
        tau /= n * circumstances[28];


        if ( Math.abs(tau) <= 1.0 ) {
            tau = sign * Math.sqrt(1.0 - (tau * tau)) * circumstances[28] / n;
        }else {
            tau = 0.0;
        }

        tau = ((circumstances[24] * circumstances[26]) + (circumstances[25] * circumstances[27])) / circumstances[30] - tau;
        circumstances[1] -= tau;

        timelocdependent(circumstances);
        iter++;
    }

    return circumstances;
}

//
// Get C1 and C4 data
//   Entry conditions -
//   1. The mid array must be populated
//   2. The magnitude at maximum eclipse must be > 0.0
function getc1c4( )
{
    var n = Math.sqrt(mid[30]);
    var tmp = (mid[26] * mid[25]) - (mid[24] * mid[27]);
    tmp = tmp / (n * mid[28]);

    if ( Math.abs(tmp) <= 1.0 )
        tmp = Math.sqrt(1.0 - (tmp * tmp)) * mid[28] / n;
    else
        tmp = 0.0;
    c1[0] = -2;
    c4[0] = 2;
    c1[1] = mid[1] - tmp;
    c4[1] = mid[1] + tmp;

    c1c4iterate(c1);
    c1c4iterate(c4);
}

//
// Iterate on C2 or C3
function c2c3iterate( circumstances )
{
    var sign, n;

    timelocdependent(circumstances);
    if (circumstances[0] < 0)
        sign = -1.0;
    else
        sign = 1.0;
    if (mid[29] < 0.0)
        sign = -sign;
    var tmp = 1.0;
    var iter = 0;
    while (((tmp > 0.000001) || (tmp < -0.000001)) && (iter < 50))
    {
        n = Math.sqrt(circumstances[30]);
        tmp = (circumstances[26] * circumstances[25]) - (circumstances[24] * circumstances[27]);
        tmp = tmp / (n * circumstances[29]);

        if ( Math.abs(tmp) <= 1.0 )
            tmp = sign * Math.sqrt(1.0 - (tmp * tmp)) * circumstances[29] / n;
        else
            tmp = 0.0;

        tmp = ((circumstances[24] * circumstances[26]) + (circumstances[25] * circumstances[27])) / circumstances[30] - tmp;
        circumstances[1] -= tmp;


        timelocdependent(circumstances);
        iter++;
    }

    return circumstances;
}

//
// Get C2 and C3 data
//   Entry conditions -
//   1. The mid array must be populated
//   2. There must be either a total or annular eclipse at the location
function getc2c3( )
{
    var n = Math.sqrt(mid[30]);
    var tmp = (mid[26] * mid[25]) - (mid[24] * mid[27]);
    tmp = tmp / (n * mid[29]);

    if ( Math.abs(tmp) <= 1.0 )
        tmp = Math.sqrt(1.0 - (tmp * tmp)) * mid[29] / n;
    else
        tmp = 0.0;

    c2[0] = -1;
    c3[0] = 1;
    if (mid[29] < 0.0)
    {
        c2[1] = mid[1] + tmp;
        c3[1] = mid[1] - tmp;
    }
    else
    {
        c2[1] = mid[1] - tmp;
        c3[1] = mid[1] + tmp;
    }

    c2c3iterate(c2);
    c2c3iterate(c3);
}

//
// Get the observational circumstances
function observational( circumstances )
{
    var contacttype;

    // We are looking at an "external" contact UNLESS this is a total solar eclipse AND we are looking at
    // c2 or c3, in which case it is an INTERNAL contact! Note that if we are looking at maximum eclipse,
    // then we may not have determined the type of eclipse (mid[39]) just yet!
    if (circumstances[0] == 0)
        contacttype = 1.0;
    else
    {
        if ((mid[39] == 3) && ((circumstances[0] == -1) || (circumstances[0] == 1)))
            contacttype = -1.0;
        else
            contacttype = 1.0;
    }
    // p
    circumstances[31] = Math.atan2(contacttype * circumstances[24], contacttype * circumstances[25]);


    // alt
    var sinlat = Math.sin(obsvconst[0]);
    var coslat = Math.cos(obsvconst[0]);
    circumstances[32] = Math.asin((circumstances[5] * sinlat) + (circumstances[6] * coslat * circumstances[18]));

    // q
    circumstances[33] = Math.asin(coslat * circumstances[17] / Math.cos(circumstances[32]));
    if (circumstances[20] < 0.0)
        circumstances[33] = Math.PI - circumstances[33];

    // v
    circumstances[34] = circumstances[31] - circumstances[33];

    // azi
    circumstances[35] = Math.atan2(-circumstances[17] * circumstances[6], (circumstances[5] * coslat) - (circumstances[18] * sinlat * circumstances[6]));

    // Visibility (take Sun radius and/or refraction into account)
    if (circumstances[32] > gRefractionHeight)
        circumstances[40] = 0;
    else
        circumstances[40] = 1;

    var xi = circumstances[19];
    var eta = circumstances[20];
    var zeta = circumstances[21];
    // Sun distance in unit of the earth equatorial radius
    var zs = (circumstances[8] * Math.cos(f1)) - (circumstances[9] * Math.cos(f2));
    zs /= Math.sin(f1) - Math.sin(f2);
    // Moon distance in unit of the earth equatorial radius
    var zm = (circumstances[8] * Math.cos(f1)) + (lambdak1k2 * circumstances[9] * Math.cos(f2));
    zm /= Math.sin(f1) + (lambdak1k2 * Math.sin(f2));
    var u = circumstances[2] - xi;
    var v = circumstances[3] - eta;
    zs -= zeta;
    zm -= zeta;
    var tmp = Math.sqrt((u * u) + (v * v) + (zs * zs));
    var sdec = (v * circumstances[6]) + (zs * circumstances[5]);
    sdec = Math.asin(sdec / tmp);
    tmp = Math.sqrt((u * u) + (v * v) + (zm * zm));
    var mdec = (v * circumstances[6]) + (zm * circumstances[5]);
    mdec = Math.asin(mdec / tmp);
    var deltamus = Math.atan(u / ((v * circumstances[5]) - (zs * circumstances[6])));
    var deltamum = Math.atan(u / ((v * circumstances[5]) - (zm * circumstances[6])));
    var sha = circumstances[7] + deltamus;
    var mha = circumstances[7] + deltamum;

    // Local hour angle
    sha -= obsvconst[1] + (elements[5] / 13713.440924999626077);
    mha -= obsvconst[1] + (elements[5] / 13713.440924999626077);

    var sinsdec = Math.sin(sdec);
    var cossdec = Math.cos(sdec);
    var sinsha = Math.sin(sha);
    var cossha = Math.cos(sha);
    // Sun altitude
    circumstances[45] = Math.asin((sinsdec * sinlat) + (cossdec * cossha * coslat));
    // Sun azimuth
    circumstances[46] = Math.atan2(-cossdec * sinsha, (sinsdec * coslat) - (cossdec * cossha * sinlat));
    var sinmdec = Math.sin(mdec);
    var cosmdec = Math.cos(mdec);
    var sinmha = Math.sin(mha);
    var cosmha = Math.cos(mha);
    // Moon altitude
    circumstances[41] = Math.asin((sinmdec * sinlat) + (cosmdec * cosmha * coslat));
    // Moon azimuth
    circumstances[42] = Math.atan2(-cosmdec * sinmha, (sinmdec * coslat) - (cosmdec * cosmha * sinlat));

    // Sun apparent radius
    tmp = (circumstances[8] * Math.cos(f1) * Math.sin(f2)) - (circumstances[9] * Math.sin(f1) * Math.cos(f2));
    var R = tmp / (Math.sin(f1) - Math.sin(f2));
    var rs = Math.asin(R / Math.sqrt((u * u) + (v * v) + (zs * zs))); // Topocentric
    circumstances[43] = rs * R2D;
    // Moon apparent radius
    var k = tmp / ((Math.sin(f1) / lambdak1k2) + Math.sin(f2));
    var rm = Math.asin(k / Math.sqrt((u * u) + (v * v) + (zm * zm))); // Topocentric
    circumstances[44] = rm * R2D;
    // Moon distance in earth radii
    circumstances[47] = zm;

    // Moon's libration
    if (circumstances[0] == 0)
    {
        var jd = getjd(circumstances);
        var jd2000 = jd - 2451545.0;
        var st = jd2000 / 36525.0;
        var st2 = st * st;
        var st3 = st2 * st;
        var st4 = st2 * st2;

        // Meeus AA page 144
        var D = rev(297.85036 + (445267.111480 * st) - (0.0019142 * st2) + (st3 / 189474.0)) * D2R;
        var M = rev(357.52772 + (35999.050340 * st) - (0.0001603 * st2) - (st3 / 300000.0)) * D2R;
        var M1 = rev(134.96298 + (477198.867398 * st) + (0.0086972 * st2) + (st3 / 56250.0)) * D2R;
        var DF = rev(93.27191 + (483202.017538 * st) - (0.0036825 * st2) + (st3 / 327270.0)) * D2R;
        var OM = rev(125.04452 - (1934.136261 * st) + (0.0020708 * st2) + (st3 / 450000.0)) * D2R;

        // Nutation in longitude
        var DeltaPsi = -(171996 + 174.2 * st) * Math.sin(OM);
        DeltaPsi -= (13187 + 1.6 * st) * Math.sin(-2 * D + 2 * DF + 2 * OM);
        DeltaPsi -= (2274 + 0.2 * st) * Math.sin(2 * DF + 2 * OM);
        DeltaPsi += (2062 + 0.2 * st) * Math.sin(2 * OM);
        DeltaPsi += (1426 - 3.4 * st) * Math.sin(M);
        DeltaPsi += (712 + 0.1 * st) * Math.sin(M1);
        DeltaPsi += (-517 + 1.2 * st) * Math.sin(-2 * D + M + 2 * DF + 2 * OM);
        DeltaPsi -= (386 + 0.4 * st) * Math.sin(2 * DF + OM);
        DeltaPsi -= 301 * Math.sin(M1 + 2 * DF + 2 * OM);
        DeltaPsi += (217 - 0.5 * st) * Math.sin(-2 * D - M + 2 * DF + 2 * OM);
        DeltaPsi -= 158 * Math.sin(-2 * D + M1);
        DeltaPsi += (129 + 0.1 * st) * Math.sin(-2 * D + 2 * DF + OM);
        DeltaPsi += 123 * Math.sin(-M1 + 2 * DF + 2 * OM);
        DeltaPsi += 63 * Math.sin(2 * D);
        DeltaPsi += (63 + 0.1 * st) * Math.sin(M1 + OM);
        DeltaPsi -= 59 * Math.sin(2 * D - M1 + 2 * DF + 2 * OM);
        DeltaPsi -= (58 + 0.1 * st) * Math.sin(-M1 + OM);
        DeltaPsi -= 51 * Math.sin(M1 + 2 * DF + OM);
        DeltaPsi += 48 * Math.sin(-2 * D + 2 * M1);
        DeltaPsi += 46 * Math.sin(-2 * M1 + 2 * DF + OM);
        DeltaPsi -= 38 * Math.sin(2 * D + 2 * DF + 2 * OM);
        DeltaPsi -= 31 * Math.sin(2 * M1 + 2 * DF + 2 * OM);
        DeltaPsi += 29 * Math.sin(2 * M1);
        DeltaPsi += 29 * Math.sin(-2 * D + M1 + 2 * DF + 2 * OM);
        DeltaPsi += 26 * Math.sin(2 * DF);
        DeltaPsi -= 22 * Math.sin(2 * DF - 2 * D);
        DeltaPsi += 21 * Math.sin(2 * DF - M1);
        DeltaPsi += (17 - 0.1 * st) * Math.sin(2 * M);
        DeltaPsi += 16 * Math.sin(2 * D - M1 + OM);
        DeltaPsi -= (16 - 0.1 * st) * Math.sin(2 * (OM + DF + M - D));
        DeltaPsi -= 15 * Math.sin(M + OM);
        DeltaPsi -= 13 * Math.sin(OM + M1 - 2 * D);
        DeltaPsi -= 12 * Math.sin(OM - M);
        DeltaPsi += 11 * Math.sin(2 * (M1 - DF));
        DeltaPsi -= 10 * Math.sin(2 * D - M1 + 2 * DF);
        DeltaPsi -= 8 * Math.sin(2 * D + M1 + 2 * DF + 2 * OM);
        DeltaPsi += 7 * Math.sin(M + 2 * DF + 2 * OM);
        DeltaPsi -= 7 * Math.sin(M + M1 - 2 * D);
        DeltaPsi -= 7 * Math.sin(2 * DF + 2 * OM - M);
        DeltaPsi -= 7 * Math.sin(2 * D + 2 * DF + OM);
        DeltaPsi += 6 * Math.sin(2 * D + M1);
        DeltaPsi += 6 * Math.sin(2 * (OM + DF + M1 - D));
        DeltaPsi += 6 * Math.sin(OM + 2 * DF + M1 - 2 * D);
        DeltaPsi -= 6 * Math.sin(2 * D - 2 * M1 + OM);
        DeltaPsi -= 6 * Math.sin(2 * D + OM);
        DeltaPsi += 5 * Math.sin(M1 - M);
        DeltaPsi -= 5 * Math.sin(OM + 2 * DF - M - 2 * D);
        DeltaPsi -= 5 * Math.sin(OM - 2 * D);
        DeltaPsi -= 5 * Math.sin(OM + 2 * DF + 2 * M1);
        DeltaPsi += 4 * Math.sin(OM - 2 * M1 - 2 * D);
        DeltaPsi += 4 * Math.sin(OM + 2 * DF + M - 2 * D);
        DeltaPsi += 4 * Math.sin(M1 - 2 * DF);
        DeltaPsi -= 4 * Math.sin(M1 - D);
        DeltaPsi -= 4 * Math.sin(M - 2 * D);
        DeltaPsi -= 4 * Math.sin(D);
        DeltaPsi += 3 * Math.sin(2 * DF + M1);
        DeltaPsi -= 3 * Math.sin(2 * (OM + DF - M1));
        DeltaPsi -= 3 * Math.sin(M1 - M - D);
        DeltaPsi -= 3 * Math.sin(M1 + M);
        DeltaPsi -= 3 * Math.sin(2 * OM + 2 * DF + M1 - M);
        DeltaPsi -= 3 * Math.sin(2 * OM + 2 * DF - M1 - M + 2 * D);
        DeltaPsi -= 3 * Math.sin(2 * OM + 2 * DF + 3 * M1);
        DeltaPsi -= 3 * Math.sin(2 * OM + 2 * DF - M + 2 * D);
        DeltaPsi *= 0.0001 / 3600.0;

        // Nutation in obliquity
        var DeltaEpsilon = (92025 + 8.9 * st) * Math.cos(OM);
        DeltaEpsilon += (5736 - 3.1 * st) * Math.cos(-2 * D + 2 * DF + 2 * OM);
        DeltaEpsilon += (977 - 0.5 * st) * Math.cos(2 * DF + 2 * OM);
        DeltaEpsilon += (-895 + 0.5 * st) * Math.cos(2 * OM);
        DeltaEpsilon += (54 - 0.1 * st) * Math.cos(M);
        DeltaEpsilon -= 7 * Math.cos(M1);
        DeltaEpsilon += (224 - 0.6 * st) * Math.cos(-2 * D + M + 2 * DF + 2 * OM);
        DeltaEpsilon += 200 * Math.cos(2 * DF + OM);
        DeltaEpsilon += (129 - 0.1 * st) * Math.cos(M1 + 2 * DF + 2 * OM);
        DeltaEpsilon += (-95 + 0.3 * st) * Math.cos(-2 * D - M + 2 * DF + 2 * OM);
        DeltaEpsilon -= 70 * Math.cos(-2 * D + 2 * DF + OM);
        DeltaEpsilon -= 53 * Math.cos(-M1 + 2 * DF + 2 * OM);
        DeltaEpsilon -= 33 * Math.cos(M1 + OM);
        DeltaEpsilon += 26 * Math.cos(2 * D - M1 + 2 * DF + 2 * OM);
        DeltaEpsilon += 32 * Math.cos(-M1 + OM);
        DeltaEpsilon += 27 * Math.cos(M1 + 2 * DF + OM);
        DeltaEpsilon -= 24 * Math.cos(-2 * M1 + 2 * DF + OM);
        DeltaEpsilon += 16 * Math.cos(2 * (D + DF + OM));
        DeltaEpsilon += 13 * Math.cos(2 * (M1 + DF + OM));
        DeltaEpsilon -= 12 * Math.cos(2 * OM + 2 * DF + M1 - 2 * D);
        DeltaEpsilon -= 10 * Math.cos(OM + 2 * DF - M1);
        DeltaEpsilon -= 8 * Math.cos(2 * D - M1 + OM);
        DeltaEpsilon += 7 * Math.cos(2 * (OM + DF + M - D));
        DeltaEpsilon += 9 * Math.cos(M + OM);
        DeltaEpsilon += 7 * Math.cos(OM + M1 - 2 * D);
        DeltaEpsilon += 6 * Math.cos(OM - M);
        DeltaEpsilon += 5 * Math.cos(OM + 2 * DF - M1 + 2 * D);
        DeltaEpsilon += 3 * Math.cos(2 * OM + 2 * DF + M1 + 2 * D);
        DeltaEpsilon -= 3 * Math.cos(2 * OM + 2 * DF + M);
        DeltaEpsilon += 3 * Math.cos(2 * OM + 2 * DF - M);
        DeltaEpsilon += 3 * Math.cos(OM + 2 * DF + 2 * D);
        DeltaEpsilon -= 3 * Math.cos(2 * (OM + DF + M1 - D));
        DeltaEpsilon -= 3 * Math.cos(OM + 2 * DF + M1 - 2 * D);
        DeltaEpsilon += 3 * Math.cos(OM - 2 * M1 + 2 * D);
        DeltaEpsilon += 3 * Math.cos(OM + 2 * D);
        DeltaEpsilon += 3 * Math.cos(OM + 2 * DF - M - 2 * D);
        DeltaEpsilon += 3 * Math.cos(OM - 2 * D);
        DeltaEpsilon += 3 * Math.cos(OM + 2 * DF + 2 * M1);
        DeltaEpsilon *= 0.0001 / 3600.0;

        var epsilon0 = (((21.448 / 60.0) + 26.0) / 60.0) + 23.0;
        var u = st / 100.0;
        var laskar = (u * (-4680.93 + (u * (-1.55 + (u * (1999.25 + (u * (-51.38 + (u * (-249.67 + (u * (-39.05 + (u * (7.12 + (u * (27.87 + (u * (5.79 + (u * 2.45))))))))))))))))))) / 3600.0;
        epsilon0 += laskar;
        var epsilon = epsilon0 + DeltaEpsilon;

        // Apparent sidereal time (Meeus AA page 88)
        var siderealTime = 280.46061837 + (360.98564736629 * jd2000) + (0.000387933 * st2) - (st3 / 38710000.0);
        siderealTime += DeltaPsi * Math.cos(epsilon * D2R);
        siderealTime = rev(siderealTime);
        localSiderealTime = siderealTime - (obsvconst[1] * R2D);
        localSiderealTime = rev(localSiderealTime);
//    var salpha = localSiderealTime - (sha * R2D);
//    salpha = rev(salpha);
        var malpha = localSiderealTime - (mha * R2D);
        malpha = rev(malpha);

        gMoonData.topoRA = malpha * D2R;
        gMoonData.topoDec = mdec;
        equatorial2ecliptical(gMoonData, epsilon * D2R);

        // Longitude of the mean ascending node (AA 47.7)
        var omega = rev(125.0445479 - (1934.1362891 * st) + (0.0020754 * st2) + (st3 / 467441) - (st4 / 60616000)) * D2R;

        // Sun's mean anomaly (AA 47.3)
        M = rev(357.5291092 + (35999.0502909 * st) - (0.0001536 * st2) + (st3 / 24490000)) * D2R;

        // Moon's mean anomaly (AA 47.4)
        M1 = rev(134.9633964 + (477198.8675055 * st) - (0.0087414 * st2) + (st3 / 69699) - (st4 / 14712000)) * D2R;

        // Moon's argument of latitude (mean distance from ascending node) (AA 47.5)
        var F = rev(93.2720950 + (483202.0175233 * st) - (0.0036539 * st2) - (st3 / 3526000) + (st4 / 863310000)) * D2R;

        // Mean elongation of the Moon from the Sun (AA 47.2)
        D = rev(297.8501921 + (445267.1114034 * st) - (0.0018819 * st2) + (st3 / 545868) - (st4 / 113065000)) * D2R;

        // Earth's eccentricity (AA 47.6)
        var E = 1.0 - (0.002516 * st) - (0.0000074 * st2);

        // Libration
        var K1 = rev(119.75 + (131.849 * st)) * D2R;
        var K2 = rev(72.56 + (20.186 * st)) * D2R;

        var rho = -0.02752 * Math.cos(M1);
        rho -= 0.02245 * Math.sin(F);
        rho += 0.00684 * Math.cos(M1 - (2.0 * F));
        rho -= 0.00293 * Math.cos(2.0 * F);
        rho -= 0.00085 * Math.cos(2.0 * (F - D));
        rho -= 0.00054 * Math.cos(M1 - (2.0 * D));
        rho -= 0.00020 * Math.sin(M1 + F);
        rho -= 0.00020 * Math.cos(M1 + (2.0 * F));
        rho -= 0.00020 * Math.cos(M1 - F);
        rho += 0.00014 * Math.cos(M1 + (2.0 * (F - D)));

        var sigma = -0.02816 * Math.sin(M1);
        sigma += 0.02244 * Math.cos(F);
        sigma -= 0.00682 * Math.sin(M1 - (2.0 * F));
        sigma -= 0.00279 * Math.sin(2.0 * F);
        sigma -= 0.00083 * Math.sin(2.0 * (F - D));
        sigma += 0.00069 * Math.sin(M1 - (2.0 * D));
        sigma += 0.00040 * Math.cos(M1 + F);
        sigma -= 0.00025 * Math.sin(2.0 * M1);
        sigma -= 0.00023 * Math.sin(M1 + (2.0 * F));
        sigma += 0.00020 * Math.cos(M1 - F);
        sigma += 0.00019 * Math.sin(M1 - F);
        sigma += 0.00013 * Math.sin(M1 + 2.0 * (F - D));
        sigma -= 0.00010 * Math.cos(M1 - (3.0 * F));

        var tau = 0.02520 * E * Math.sin(M);
        tau += 0.00473 * Math.sin(2.0 * (M1 - F));
        tau -= 0.00467 * Math.sin(M1);
        tau += 0.00396 * Math.sin(K1);
        tau += 0.00276 * Math.sin(2.0 * (M1 - D));
        tau += 0.00196 * Math.sin(omega);
        tau -= 0.00183 * Math.cos(M1 - F);
        tau += 0.00115 * Math.sin(M1 - (2.0 * D));
        tau -= 0.00096 * Math.sin(M1 - D);
        tau += 0.00046 * Math.sin(2.0 * (F - D));
        tau -= 0.00039 * Math.sin(M1 - F);
        tau -= 0.00032 * Math.sin(M1 - M - D);
        tau += 0.00027 * Math.sin(2.0 * (M1 - D) - M);
        tau += 0.00023 * Math.sin(K2);
        tau -= 0.00014 * Math.sin(2.0 * D);
        tau += 0.00014 * Math.cos(2.0 * (M1 - F));
        tau -= 0.00012 * Math.sin(M1 - (2.0 * F));
        tau -= 0.00012 * Math.sin(2.0 * M1);
        tau += 0.00011 * Math.sin(2.0 * (M1 - M - D));

        var I = 1.54242 * D2R;
        var W = gMoonData.lambda - (DeltaPsi * D2R) - omega;
        W = revrad(W);
        // Optical libration in longitude
        var A = Math.atan2((Math.sin(W) * Math.cos(gMoonData.beta) * Math.cos(I)) - (Math.sin(gMoonData.beta) * Math.sin(I)), Math.cos(W) * Math.cos(gMoonData.beta));
        A = revrad(A);
        l = A - F;
        // Optical libration in latitude
        var b = Math.asin((-Math.sin(W) * Math.cos(gMoonData.beta) * Math.sin(I)) - (Math.sin(gMoonData.beta) * Math.cos(I)));
        // Physical libration in longitude
        var l2 = -tau + ((rho * Math.cos(A)) + (sigma * Math.sin(A))) * Math.tan(b);
        // Physical libration in latitude
        var b2 = (sigma * Math.cos(A)) - (rho * Math.sin(A));

        tmp = (l * R2D) + l2;
        if (tmp > 9.0)
            tmp -= 360.0;
        else if (tmp < -9.0)
            tmp += 360.0;
        mid[48] = tmp;
        mid[49] = (b * R2D) + b2;

        // Polar angle
        rho *= D2R;
        var V = omega + (DeltaPsi * D2R) + (sigma * D2R / Math.sin(I));
        var X = Math.sin(I + rho) * Math.sin(V);
        var Y = (Math.sin(I + rho) * Math.cos(V) * Math.cos(epsilon * D2R)) - (Math.cos(I + rho) * Math.sin(epsilon * D2R));
        W = Math.atan2(X, Y);
        W = revrad(W);
        var PA = revrad(Math.asin(Math.sqrt((X * X) + (Y * Y)) * Math.cos((malpha * D2R) - W) / Math.cos(b)));

        mid[50] = PA * R2D;

        mid[51] = getsn(jd);	// Sun axis from celestial north
    }
    else
    {
        var jd = getjd(circumstances);
        circumstances[51] = getsn(jd);	// Sun axis from celestial north
    }

    return circumstances;
}

//
// Return an angle between 0 and 360 degrees
function rev( angle )
{
    return angle - (360.0 * Math.floor(angle / 360.0));
}

//
// Return an angle between 0 and 2PI radians
function revrad( angle )
{
    return angle - ((Math.PI * 2.0) * Math.floor(angle / (Math.PI * 2.0)));
}

function equatorial2ecliptical( obj, obliquity )
{
    obj.lambda = Math.atan2((Math.sin(obj.topoRA) * Math.cos(obliquity)) + (Math.tan(obj.topoDec) * Math.sin(obliquity)), Math.cos(obj.topoRA));
    obj.lambda = revrad(obj.lambda);

    obj.beta = Math.asin((Math.sin(obj.topoDec) * Math.cos(obliquity)) - (Math.cos(obj.topoDec) * Math.sin(obliquity) * Math.sin(obj.topoRA)));
}

//
// Calculate maximum eclipse
function getmid( )
{
    mid[0] = 0;
    mid[1] = 0.0;
    var iter = 0;
    var tmp = 1.0;
    timelocdependent(mid);
    while (((tmp > 0.000001) || (tmp < -0.000001)) && (iter < 50))
    {
        tmp = ((mid[24] * mid[26]) + (mid[25] * mid[27])) / mid[30];

        mid[1] -= tmp;
        timelocdependent(mid);
        iter++;
    }
}

//
// Populate the c1, c2, mid, c3 and c4 arrays
function getall( language, limbCorrections )
{
    f1 = Math.atan(elements[26]);
    f2 = Math.atan(elements[27]);

    getmid();
    observational(mid);
    PV[2] = getpv(mid);
    // m, magnitude and Moon/Sun ratio
    mid[36] = Math.sqrt((mid[24] * mid[24]) + (mid[25] * mid[25]));
    mid[37] = (mid[28] - mid[36]) / (mid[28] + mid[29]);
    mid[38] = (mid[28] - mid[29]) / (mid[28] + mid[29]);
console.log("MSR: " + mid[38]);
    if (mid[37] > 0.0)
    {
        getc1c4();
        if ((mid[36] < mid[29]) || (mid[36] < -mid[29]))
        {
            getc2c3();

            if (mid[29] < 0.0)
                mid[39] = 3; // Total solar eclipse
            else
                mid[39] = 2; // Annular solar eclipse
            observational(c2);
            V[0] = rev(360.0 - (c2[34] * R2D));
            PV[1] = getpv(c2);
            observational(c3);
            V[1] = rev(360.0 - (c3[34] * R2D));
            PV[3] = getpv(c3);
            c2[36] = 999.9;
            c3[36] = 999.9;

            var VSOP = 1;
            var Mes = 0;
            if ( location.search.length > 1 )
            {
                var argstr = location.search.substring(1, location.search.length);
                var args = argstr.split("&");
                for ( var i = 0; i < args.length; i++ )
                {
                    if ( args[i].substring(0, 4) == "Mes=" )
                    {
                        eval(unescape(args[i]));
                        if ( Mes != 1 )
                            Mes = 0;
                    }
                    if ( args[i].substring(0, 5) == "VSOP=" )
                    {
                        eval(unescape(args[i]));
                        if ( VSOP != 1 )
                            VSOP = 0;
                    }
                }
            }
            gMes = Mes;
            gVSOP = VSOP;
            if (gVSOP == 0)
                lambdak1k2 = 1.00083222847; // = k1/k2 with k1 = 0.2725076 and k2 = 0.2722810 (IAU 1982)
            else
                lambdak1k2 = 1.00076024401; // = k1/k2 with k1 = 0.2724880 and k2 = 0.2722810 or 1.00076026356 with 0.272481 and 0.272274

            if ( limbCorrections )
                loadXMLCalc('http://xjubier.free.fr/php/php5/WattsChartCorrections.php?RM=' + mid[44] + '&ZM=' + mid[47] + '&RS=' + mid[43] + '&LibL=' + mid[48].toFixed(2) + '&LibB=' + mid[49].toFixed(2) + '&MN=' + mid[50].toFixed(2) + '&PA2=' + (rev(c2[31] * R2D).toFixed(2)) + '&PA3=' + (rev(c3[31] * R2D).toFixed(2)) + '&Dur=' + getdurationseconds() + '&Mes=' + gMes + '&VSOP=' + gVSOP + '&Lang=' + language, true);  // Always asynchronous for performance purposes
        }
        else
            mid[39] = 1; // Partial eclipse
        observational(c1);
        PV[0] = getpv(c1);
        observational(c4);
        PV[4] = getpv(c4);
    }
    else
        mid[39] = 0; // No eclipse

    c1_alt[0] = c1[45] * R2D; // Sun
    c1_azi[0] = c1[46] * R2D;
    if (c1_azi[0] < 0.0)
        c1_azi[0] += 360.0;
    else if (c1_azi[0] >= 360.0)
        c1_azi[0] -= 360.0;
    c1_rad[0] = c1[43] * 100;
    c2_alt[0] = c2[45] * R2D;
    c2_azi[0] = c2[46] * R2D;
    if (c2_azi[0] < 0.0)
        c2_azi[0] += 360.0;
    else if (c2_azi[0] >= 360.0)
        c2_azi[0] -= 360.0;
    c2_rad[0] = c2[43] * 100;
    mid_alt[0] = mid[45] * R2D;
    mid_azi[0] = mid[46] * R2D;
    if (mid_azi[0] < 0.0)
        mid_azi[0] += 360.0;
    else if (mid_azi[0] >= 360.0)
        mid_azi[0] -= 360.0;
    mid_rad[0] = mid[43] * 100;
    c3_alt[0] = c3[45] * R2D;
    c3_azi[0] = c3[46] * R2D;
    if (c3_azi[0] < 0.0)
        c3_azi[0] += 360.0;
    else if (c3_azi[0] >= 360.0)
        c3_azi[0] -= 360.0;
    c3_rad[0] = c3[43] * 100;
    c4_alt[0] = c4[45] * R2D;
    c4_azi[0] = c4[46] * R2D;
    if (c4_azi[0] < 0.0)
        c4_azi[0] += 360.0;
    else if (c4_azi[0] >= 360.0)
        c4_azi[0] -= 360.0;
    c4_rad[0] = c4[43] * 100;

    c1_alt[1] = c1[41] * R2D; // Moon
    c1_azi[1] = c1[42] * R2D;
    if (c1_azi[1] < 0.0)
        c1_azi[1] += 360.0;
    else if (c1_azi[1] >= 360.0)
        c1_azi[1] -= 360.0;
    c1_rad[1] = c1[44] * 100;
    c2_alt[1] = c2[41] * R2D;
    c2_azi[1] = c2[42] * R2D;
    if (c2_azi[1] < 0.0)
        c2_azi[1] += 360.0;
    else if (c2_azi[1] >= 360.0)
        c2_azi[1] -= 360.0;
    c2_rad[1] = c2[44] * 100;
    mid_alt[1] = mid[41] * R2D;
    mid_azi[1] = mid[42] * R2D;
    if (mid_azi[1] < 0.0)
        mid_azi[1] += 360.0;
    else if (mid_azi[1] >= 360.0)
        mid_azi[1] -= 360.0;
    mid_rad[1] = mid[44] * 100;
    c3_alt[1] = c3[41] * R2D;
    c3_azi[1] = c3[42] * R2D;
    if (c3_azi[1] < 0.0)
        c3_azi[1] += 360.0;
    else if (c3_azi[1] >= 360.0)
        c3_azi[1] -= 360.0;
    c3_rad[1] = c3[44] * 100;
    c4_alt[1] = c4[41] * R2D;
    c4_azi[1] = c4[42] * R2D;
    if (c4_azi[1] < 0.0)
        c4_azi[1] += 360.0;
    else if (c4_azi[1] >= 360.0)
        c4_azi[1] -= 360.0;
    c4_rad[1] = c4[44] * 100;
}

//
// Read the data, and populate the obsvconst array
function readdata( lat, lon, elv )
{
    // Get the latitude
    obsvconst[0] = lat * D2R;

    // Get the longitude
    obsvconst[1] = -lon * D2R;

    var Elv = 0.0;
    if ( typeof window.TZ !== "undefined" )
        var TZ = window.TZ;
    else
        var TZ = 0.0;
    if ( ( elv == -1.0 ) || ( elv == 0.0 ) )
    {
        if (location.search.length > 1)
        {
            var argstr = location.search.substring(1, location.search.length);
            var args = argstr.split("&");
            for (var i = 0; i < args.length; i++)
            {
                if ( args[i].substring(0, 4) == "Elv=" )
                    eval(unescape(args[i]));
                else if ( args[i].substring(0, 3) == "TZ=" )
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

    // Get the observer's geocentric position
    var tmp = Math.atan(0.996647189335 * Math.tan(obsvconst[0]));
    obsvconst[4] = (0.996647189335 * Math.sin(tmp)) + (obsvconst[2] * Math.sin(obsvconst[0]) / 6378137.0);
    obsvconst[5] = Math.cos(tmp) + (obsvconst[2] * Math.cos(obsvconst[0]) / 6378137.0);
}

//
// Read the data for the geolocation, and populate the obsvconst array
function readdata_geo( lat, lon, elv )
{
    // Get the latitude
    obsvconst[0] = lat * D2R;

    // Get the longitude
    obsvconst[1] = -lon * D2R;

    // Get the altitude (sea level by default)
    obsvconst[2] = elv;

    // Get the time zone (UT by default)
    obsvconst[3] = 0.0;

    // Get the observer's geocentric position
    var tmp = Math.atan(0.996647189335 * Math.tan(obsvconst[0]));
    obsvconst[4] = (0.996647189335 * Math.sin(tmp)) + (obsvconst[2] * Math.sin(obsvconst[0]) / 6378137.0);
    obsvconst[5] = Math.cos(tmp) + (obsvconst[2] * Math.cos(obsvconst[0]) / 6378137.0);
}

//
// Read the deltaT value for the selected eclipse
function getdTValue( decimals )
{
    var deltaT = elements[5] + 0.0;

    return deltaT.toFixed(decimals);
}

//
// Get the local date of an event (see AA p.63 or http://aa.usno.navy.mil/js/JulianDate.js)
function getdate( circumstances, language )
{
    var jd, t, ans, a, b, c, d, e, year, sign;

    // JD for noon (TDT) the day before the day that contains T0
    jd = Math.floor(elements[0] - (elements[1] / 24.0));
    // Local time (ie the offset in hours since midnight TDT on the day containing T0) to the nearest 0.1 sec
    t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
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
    // Local time (ie the offset in hours since midnight TDT on the day containing T0) to the nearest 0.1 sec
    t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
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
// Get the UT date of an event
function getnumUTdate( circumstances, numDate )
{
    var jd, t, a, b, c, d, e;

    // JD for noon (TDT) the day before the day that contains T0
    jd = Math.floor(elements[0] - (elements[1] / 24.0));
    // Local time (ie the offset in hours since midnight TDT on the day containing T0) to the nearest 0.1 sec
//  t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
    // UT time (ie the offset in hours since midnight TDT on the day containing T0) to the nearest 0.1 sec
    t = circumstances[1] + elements[1] - ((elements[5] - 0.05) / 3600.0);
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
        numDate.year = c - 4716;
    else
        numDate.year = c - 4715;
    numDate.month = e;
    numDate.day = d;
}

//
// Get the local time of an event
function gettime( circumstances, language )
{
    var ans = "";
    // Local time to the nearest 0.1 sec
    var t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
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
    if ( language == "fr" )
        ans += ",";
    else
        ans += ".";
    ans += Math.floor(10.0 * (t - Math.floor(t)));
    // Add an asterix if the altitude is less than zero (take Sun radius and/or refraction into account)
    if (circumstances[32] <= gRefractionHeight)
        ans += "*";

    return ans;
}

//
// Get the local time with limb correction of an event (must be at c2 or c3)
function gettimelc( circumstances, language )
{
    var ans = "";
    // Local time to the nearest 0.1 sec
    var t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
    if (((circumstances[0] == 1) || (circumstances[0] == -1)) && (circumstances[36] < 999.9))
    {
        if ((c3[1] + (c3[36] / 3600.0)) >= (c2[1] + (c2[36] / 3600.0)))
            t += circumstances[36] / 3600.0;
        else
            t = mid[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
    }
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
    if ( language == "fr" )
        ans += ",";
    else
        ans += ".";
    ans += Math.floor(10.0 * (t - Math.floor(t)));

    return ans;
}

//
// Get the shorten local time of an event
function gettimeshort( circumstances )
{
    var ans = "";
    // Local time to the nearest 0.1 sec
    var t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
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
    // Local time to the nearest 0.1 sec
    var t = circumstances[1] + elements[1] - obsvconst[3] - ((elements[5] - 0.05) / 3600.0);
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
    // Add an asterix if the altitude is less than zero (take Sun radius and/or refraction into account)
    if (circumstances[32] <= gRefractionHeight)
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
        t = circumstances[1] + elements[1] - ((elements[5] - 0.05) / 3600.0);
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
        jd = elements[0] - (elements[5] / 86400.0) + 0.5;
        var z = Math.floor(jd);
        var f = jd - z;
        t = 12.0 + (24.0 * ((elements[0] - (elements[5] / 86400.0)) - Math.floor(elements[0] - (elements[5] / 86400.0))));
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
// Julian day from the beginning of the year -4712 at noon UT (valid only for positive Julian day)
// (Meeus AA page 60)
function getjd( circumstances )
{
    var y, m, a, b;
    var numDate = new Object();

    getnumUTdate(circumstances, numDate);
    numDate.time = elements[1] + circumstances[1] - (elements[5] / 3600.0);
    if (numDate.time < 0.0)
        numDate.time += 24.0;
    else if (numDate.time >= 24.0)
        numDate.time -= 24.0;

    var gregorian = true;
    if ( numDate.year < 1582 )
        gregorian = false;
    else if ( numDate.year == 1582 )
    {
        if ( ( numDate.month < 10 ) || ( ( numDate.month == 10 ) && ( numDate.day < 15 ) ) )
            gregorian = false;
    }
    if ( numDate.month > 2 )
    {
        y = numDate.year;
        m = numDate.month;
    }
    else
    {
        y = numDate.year - 1;
        m = numDate.month + 12;
    }

    a = truncate(y / 100);
    if ( gregorian )
        b = 2 - a + truncate(a / 4);
    else
        b = 0.0;
    var jd = truncate(365.25 * (y + 4716)) + truncate(30.6001 * (m + 1)) + numDate.day + b - 1524.5;
    jd += numDate.time / 24.0;

    return jd;
}

//
// Get the altitude
function getalt( circumstances, language )
{
    var ans;

    var t = circumstances[32] * R2D;
    if (t < 0.0)
    {
        ans = "-";
        t = -t;
    }
    else
        ans = "+";
    t += 0.05;
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
    var t = circumstances[35] * R2D;
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
// Get P
function getp( circumstances )
{
    var ans = "";
    var t = circumstances[31] * R2D;
    if (t < 0.0)
        t += 360.0;
    else if (t >= 360.0)
        t -= 360.0;
    t = Math.floor(t + 0.5);
    if (t < 100.0)
        ans += "0";
    if (t < 10.0)
        ans += "0";
    ans += t;

    return ans;
}

//
// Get V
function getv( circumstances, language )
{
    var ans = "";
    var t = Math.floor(120.5 - circumstances[34] * 60.0 / Math.PI) / 10.0;
    while (t > 13.0)
        t -= 12.0;
    while (t < 1.0)
        t += 12.0;
    if (t < 10.0)
        ans += "0";
    ans += Math.floor(t);
    if ( language == "fr" )
        ans += ",";
    else
        ans += ".";
    ans += Math.floor(t * 10.0 - 10.0 * Math.floor(t)).toString();

    return ans;
}

function getpv( circumstances )
{
    var p = circumstances[31] * R2D;
    while (p < 0.0)
        p += 360.0;
    while (p >= 360.0)
        p -= 360.0;

    var v = 360 - (circumstances[34] * R2D);
    while (v < 0.0)
        v += 360.0;
    while (v >= 360.0)
        v -= 360.0;

    var ans = p + v;
    while (ans < 0.0)
        ans += 360.0;
    while (ans >= 360.0)
        ans -= 360.0;

    return ans;
}

//
// Sun axis from celestial north
function getsn( jd )
{
    var t = (jd - 2396758.0) / 36525.0;	// Number of centuries since 1850 (1849 December 31 at 12UT)
    var T = (jd - 2415020.0) / 36525.0;	// Number of centuries since 1 Jan 1900 noon ET (1899 December 31 at 12UT)
    var kks = 73.666667 + (1.3958333 * t);	// Longitude of the ascending node of the solar equator on the ecliptic
    var kkm = 259.183275 - ((1934.142008 - (0.002078 * T)) * T);	// Longitude of the ascending node of the Moon orbit
    var G = (0.0000739 * Math.sin((31.8 + (119.0 * T)) * D2R)) + (0.0017778 * Math.sin((231.19 + (20.2 * T)) * D2R)) + (0.00052 * Math.sin((57.24 + (150.27 * T)) * D2R));	// Long term corrections on the solar longitude
    var L = 279.696678 + ((36000.768925 + (0.0003025 * T)) * T) + G;	// Mean longitude of the Sun
    var M = 358.475833 + ((35999.04975 - (0.00015 * T)) * T) + G;	// Mean anomaly of the Sun
    var C = ((1.9194603 - (0.0047889 * T) - (0.0000144 * T * T)) * Math.sin(M * D2R)) + ((0.0200939 - 0.0001003 * T) * Math.sin(2.0 * M * D2R)) + (0.0002925 * Math.sin(3.0 * M * D2R)) + (0.000005 * Math.sin(4.0 * M * D2R));	// Equation of the center
    var v = M + C;	// True anomaly
    var lambda = L + C - (0.0056933 * (1.0 + (0.01671 * Math.cos(v * D2R))));	// Apparent longitude of the Sun (without nutation)
    var nutL = (-0.00479 * Math.sin(kkm * D2R)) - (0.00035 * Math.sin(2.0 * L * D2R));	// Nutation in longitude
    var nutI = (0.00256 * Math.cos(kkm * D2R)) + (0.00015 * Math.cos(2.0 * L * D2R));	// Nutation in obliquity
    var lambdaS = lambda + nutL;	// Apparent longitude of the Sun with nutation
    var epsilon = 23.452294 - ((0.0130125 + (0.0000016 * T)) * T) + nutI;	// Obliquity of the ecliptic
    var i = 7.25 * D2R;	// Inclination of the solar equator to the ecliptic
    var lambdamK = (lambda - kks) * D2R;
    var x = Math.atan(-Math.cos(lambdaS * D2R) * Math.tan(epsilon * D2R)) * R2D;	// +/- 90 degrees
    var y = Math.atan(-Math.cos(lambdamK) * Math.tan(i)) * R2D;	// +/- 90 degrees

    return(rev(x + y));	// Solar position angle
}

//
// Get the limb correction
function getlc( circumstances, language )
{
    var ans = "";
    if (((circumstances[0] == 1) || (circumstances[0] == -1)) && (circumstances[36] < 999.9))
    {
        var t;

        if (circumstances[36] < 0.0)
        {
            ans = "-";
            t = -circumstances[36];
        }
        else
        {
            ans = "+";
            t = circumstances[36];
        }
        t = Math.floor(t * 10.0 + 0.5) / 10.0;
        ans += Math.floor(t).toString();
        if ( language == "fr" )
            ans += ",";
        else
            ans += ".";
        ans += Math.floor(t * 10.0 - 10.0 * Math.floor(t)).toString();
        ans += "s";
    }

    return ans;
}

//
// Display the information about 1st contact
function displayc1( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'C1\', false, ' + (( language == "fr" ) ? '\'fr\'' : '\'en\'') + '); shadowOutlineLowAccuracy(c1[1]);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += "D&eacute;but&nbsp;de&nbsp;l&rsquo;&eacute;clipse&nbsp;partielle";
    else
        html += "Start&nbsp;of&nbsp;partial&nbsp;eclipse";
    var alt = c1[32] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(C1)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(c1, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(c1, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(c1, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(c1, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getp(c1) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getv(c1, language) + '</td>' + (( mid[39] > 1 ) ? '<td class="Eclipse">&nbsp;</td>' : '') + '</tr>';

    return html;
}

//
// Display the information about 2nd contact
function displayc2( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'C2\', false, ' + (( language == "fr" ) ? '\'fr\'' : '\'en\'') + '); shadowOutlineLowAccuracy(c2[1]);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
    {
        if ( mid[39] > 2 )
            html += "D&eacute;but&nbsp;de&nbsp;l&rsquo;&eacute;clipse&nbsp;totale";
        else
            html += "D&eacute;but&nbsp;de&nbsp;l&rsquo;&eacute;clipse&nbsp;annulaire";
    }
    else
    {
        if ( mid[39] > 2 )
            html += "Start&nbsp;of&nbsp;total&nbsp;eclipse";
        else
            html += "Start&nbsp;of&nbsp;annular&nbsp;eclipse";
    }
    var alt = c2[32] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(C2)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(c2, language) + '</td><td class="EclipseLeft" nowrap="nowrap"><span id="c2_time" class="EclipseLeft">' + gettime(c2, language) + '</span></td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(c2, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(c2, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getp(c2) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getv(c2, language) + '</td><td class="EclipseRight" nowrap="nowrap"><div id="c2_lc" class="EclipseRight">' + getlc(c2, language) + '</div></td></tr>';

    return html;
}

//
// Display the information about maximum eclipse
function displaymid( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'mid\', false, ' + (( language == "fr" ) ? '\'fr\'' : '\'en\'') + '); shadowOutlineLowAccuracy(mid[1]);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += "Maximum&nbsp;de&nbsp;l&rsquo;&eacute;clipse";
    else
        html += "Maximum&nbsp;eclipse";
    var alt = mid[32] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    gMaximumEclipseAltitude = true_alt;
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    gMaximumEclipseAzimuth = getazi(mid, "en");
    html += '&nbsp;(MAX)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(mid, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(mid, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(mid, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(mid, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getp(mid) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getv(mid, language) + '</td>' + (( mid[39] > 1 ) ? '<td class="Eclipse">&nbsp;</td>' : '') + '</tr>';

    return html;
}

//
// Display the information about 3rd contact
function displayc3( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'C3\', false, ' + (( language == "fr" ) ? '\'fr\'' : '\'en\'') + '); shadowOutlineLowAccuracy(c3[1]);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
    {
        if ( mid[39] > 2 )
            html += "Fin&nbsp;de&nbsp;l&rsquo;&eacute;clipse&nbsp;totale";
        else
            html += "Fin&nbsp;de&nbsp;l&rsquo;&eacute;clipse&nbsp;annulaire";
    }
    else
    {
        if ( mid[39] > 2 )
            html += "End&nbsp;of&nbsp;total&nbsp;eclipse";
        else
            html += "End&nbsp;of&nbsp;annular&nbsp;eclipse";
    }
    var alt = c3[32] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(C3)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(c3, language) + '</td><td class="EclipseLeft" nowrap="nowrap"><span id="c3_time" class="EclipseLeft">' + gettime(c3, language) + '</span></td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(c3, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(c3, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getp(c3) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getv(c3, language) + '</td><td class="EclipseRight" nowrap="nowrap"><div id="c3_lc" class="EclipseRight">' + getlc(c3, language) + '</div></td></tr>';

    return html;
}

//
// Display the information about 4th contact
function displayc4( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'C4\', false, ' + (( language == "fr" ) ? '\'fr\'' : '\'en\'') + '); shadowOutlineLowAccuracy(c4[1]);" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += "Fin&nbsp;de&nbsp;l&rsquo;&eacute;clipse&nbsp;partielle";
    else
        html += "End&nbsp;of&nbsp;partial&nbsp;eclipse";
    var alt = c4[32] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(C4)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(c4, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettime(c4, language) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(c4, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(c4, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getp(c4) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getv(c4, language) + '</td>' + (( mid[39] > 1 ) ? '<td class="Eclipse">&nbsp;</td>' : '') + '</tr>';

    return html;
}

//
// Display the information about sunrise
function displaysunrise( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'Sunrise\', false, ' + (( language == "fr" ) ? '\'fr\'' : '\'en\'') + ');" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += "Lever&nbsp;du&nbsp;soleil";
    else
        html += "Sunrise";
    var alt = sunrise[32] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(RISE)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(sunrise, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettimeshort(sunrise) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(sunrise, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(sunrise, language) + '&deg;</td><td colspan="2" class="EclipseCenter"' + (( ( sunrise[1] >= c1[1] ) && ( sunrise[1] != mid[1]) ) ? ' title="' + (( language == "fr" ) ? 'Degr&eacute; d&rsquo;obscurit&eacute; au lever du soleil (grandeur: ' + sunrise[37].toFixed(5).replace(/\./, ',') + ')' : 'Obscuration at sunrise (magnitude: ' + sunrise[37].toFixed(5) + ')') + '"' : '') + ' nowrap="nowrap">' + (( ( sunrise[1] >= c1[1] ) && ( sunrise[1] != mid[1]) ) ? getcoveragesunrisesunset(sunrise, language) : '&nbsp;') + '</td>' + (( mid[39] > 1 ) ? '<td class="Eclipse">&nbsp;</td>' : '') + '</tr>';

    return html;
}

//
// Display the information about sunset
function displaysunset( language )
{
    var html = '<tr onmouseover="this.style.backgroundColor = \'#D8E4F1\'; drawDiagram(\'Sunset\', false, ' + (( language == "fr" ) ? '\'fr\'' : '\'en\'') + ');" onmouseout="this.style.backgroundColor = \'#FDF3D0\';"><td align="right" class="EclipseRight" nowrap="nowrap">';
    if ( language == "fr" )
        html += "Coucher&nbsp;du&nbsp;soleil";
    else
        html += "Sunset";
    var alt = sunset[32] * R2D;
    var true_alt = elevationRefraction(alt);
    true_alt = true_alt.toFixed(1);
    if ( language == "fr" )
        true_alt = true_alt.replace(/\./, ',');
    html += '&nbsp;(SET)&nbsp;:&nbsp;</td><td class="' + (( language == "fr" ) ? 'EclipseRight' : 'EclipseLeft') + '" nowrap="nowrap">' + getdate(sunset, language) + '</td><td class="EclipseLeft" nowrap="nowrap">' + gettimeshort(sunset) + '</td><td class="EclipseRight" ' + ((alt > -0.3) ? 'title="' + true_alt + '&deg; ' + (( language == "fr" ) ? 'avec la r&eacute;fraction' : 'with refraction') + '" ' : ' ') + 'nowrap="nowrap">' + getalt(sunset, language) + '&deg;</td><td class="EclipseRight" nowrap="nowrap">' + getazi(sunset, language) + '&deg;</td><td colspan="2" class="EclipseCenter"' + (( ( sunset[1] <= c4[1] ) && ( sunset[1] != mid[1]) ) ? ' title="' + (( language == "fr" ) ? 'Degr&eacute; d&rsquo;obscurit&eacute; au coucher du soleil (grandeur: ' + sunset[37].toFixed(5).replace(/\./, ',') + ')' : 'Obscuration at sunset (magnitude: ' + sunset[37].toFixed(5) + ')') + '"' : '') + ' nowrap="nowrap">' + (( ( sunset[1] <= c4[1] ) && ( sunset[1] != mid[1]) ) ? getcoveragesunrisesunset(sunset, language) : '&nbsp;') + '</td>' + (( mid[39] > 1 ) ? '<td class="Eclipse">&nbsp;</td>' : '') + '</tr>';

    return html;
}

//
// Get the duration in 00m 00.0s format
function getduration( language )
{
    var tmp = c3[1] - c2[1];
    if (tmp < 0.0)
        tmp += 24.0;
    else if (tmp >= 24.0)
        tmp -= 24.0;
    tmp = (tmp * 60.0) - 60.0 * Math.floor(tmp) + 0.05 / 60.0;

    var ans = Math.floor(tmp) + "m&nbsp;";
    tmp = (tmp * 60.0) - 60.0 * Math.floor(tmp);
    if (tmp < 10.0)
        ans += "0";
    ans += Math.floor(tmp);
    if ( language == "fr" )
        ans += ",";
    else
        ans += ".";
    ans += Math.floor((tmp - Math.floor(tmp)) * 10.0).toString() + "s";
    return ans;
}

//
// Get the limb corrected duration in 00m 00.0s format
function getdurationlc( language )
{
    var ans = "";

    var tmp = c3[1] - c2[1];
    if (tmp < 0.0)
        tmp += 24.0;
    else if (tmp >= 24.0)
        tmp -= 24.0;
    if (hasValidLimbCorrections() == true)
    {
        tmp += (c3[36] - c2[36]) / 3600.0;
        if (tmp > 0.0)
        {
            tmp = (tmp * 60.0) - 60.0 * Math.floor(tmp) + 0.05 / 60.0;
            ans += Math.floor(tmp) + "m&nbsp;";
            tmp = (tmp * 60.0) - 60.0 * Math.floor(tmp);
            if (tmp < 10.0)
                ans += "0";
            ans += Math.floor(tmp);
            if ( language == "fr" )
                ans += ",";
            else
                ans += ".";
            ans += Math.floor((tmp - Math.floor(tmp)) * 10.0).toString() + "s";
        }
        else
        {
            if ((mid[38] > 0.999) && (mid[38] < 1.0001) && (c2[36] != 999.9) && (c3[36] != 999.9))
            {
                if ( language == "fr" )
                    ans = "0m&nbsp;00,0s \xABperl\xE9e\xBB";
                else
                    ans = "0m&nbsp;00.0s \"beaded\"";
            }
            else if ((mid[38] > 0.997) && (mid[38] <= 0.999) && (mid[39] == 2) && (c2[36] != 999.9) && (c3[36] != 999.9))
            {
                if ( language == "fr" )
                    ans = "0m&nbsp;00,0s \xABbris\xE9e\xBB";
                else
                    ans = "0m&nbsp;00.0s \"broken\"";
            }
            else
            {
                if ( language == "fr" )
                    ans = "0m&nbsp;00,0s";
                else
                    ans = "0m&nbsp;00.0s";
            }
        }
        if ( language == "fr" )
            ans += " (dur&eacute;e corrig&eacute;e limbe)";
        else
            ans += " (lunar limb corrected)";
    }
    else
    {
        if (((tmp * 3600.0) < 10.0) && (mid[38] > 0.999) && (mid[38] < 1.0001) && (c2[36] != 999.9) && (c3[36] != 999.9))
        {
            if ( language == "fr" )
                ans = "0m&nbsp;00,0s (&eacute;clipse perl&eacute;e)";
            else
                ans = "0m&nbsp;00.0s (beaded eclipse)";
        }
        else if (((tmp * 3600.0) <= 32.0) && (c2[36] != 999.9) && (c3[36] != 999.9))
        {
            if ( language == "fr" )
            {
                if (gMes == 0)
                    ans = "0m&nbsp;00,0s (dur&eacute;e corrig&eacute;e limbe)";
                else
                    ans = "0m&nbsp;00,0s (dur&eacute;e corrig&eacute;e limbe + m&eacute;sosph&eacute;rique)";
            }
            else
            {
                if (gMes == 0)
                    ans = "0m&nbsp;00.0s (lunar limb corrected)";
                else
                    ans = "0m&nbsp;00.0s (lunar limb corrected + mesospheric)";
            }
        }
    }

    return ans;
}

//
// Get the duration in seconds
function getdurationseconds( )
{
    var tmp = c3[1] - c2[1];
    if (tmp < 0.0)
        tmp = 0.0;
    else if (tmp >= 24.0)
        tmp -= 24.0;
    tmp *= 3600.0;

    return tmp.toFixed(3);
}

//
// Get the obscuration
function getcoverage( language )
{
    var a, b, c;

    if (mid[37] <= 0.0)
    {
        if ( language == "fr" )
            return "0,00%";
        else
            return "0.00%";
    }
    else if (mid[37] >= 1.0)
    {
        if ( language == "fr" )
            return "100,00%";
        else
            return "100.00%";
    }


    if (mid[39] == 2)
        c = mid[38] * mid[38];
    else
    {

        c = Math.acos((mid[28] * mid[28] + mid[29] * mid[29] - 2.0 * mid[36] * mid[36]) / (mid[28] * mid[28] - mid[29] * mid[29]));
        b = Math.acos((mid[28] * mid[29] + mid[36] * mid[36]) / mid[36] / (mid[28] + mid[29]));
        a = Math.PI - b - c;
        c = ((mid[38] * mid[38] * a + b) - mid[38] * Math.sin(c)) / Math.PI;
    }

    var ans = (c * 100).toFixed(3);
    if ( language == "fr" )
        ans = ans.replace(/\./, ',');
    ans += "%";

    return ans;
}

//
// Get the obscuration at sunrise/sunset
function getcoveragesunrisesunset( circumstances, language )
{
    var a, b, c, ans;

    c = Math.acos((circumstances[28] * circumstances[28] + circumstances[29] * circumstances[29] - 2.0 * circumstances[36] * circumstances[36]) / (circumstances[28] * circumstances[28] - circumstances[29] * circumstances[29]));
    b = Math.acos((circumstances[28] * circumstances[29] + circumstances[36] * circumstances[36]) / circumstances[36] / (circumstances[28] + circumstances[29]));
    if ( ( isNaN(c) ) || ( isNaN(b) ) )
        ans = "";
    else
    {
        a = Math.PI - b - c;
        c = ((circumstances[38] * circumstances[38] * a + b) - circumstances[38] * Math.sin(c)) / Math.PI;

        ans = (c * 100).toFixed(2);
        if ( language == "fr" )
            ans = ans.replace(/\./, ',');
        ans += "%";
    }

    return ans;
}

//
// Get the (Ant)Umbral depth
// Entry condition - there is a total or annular eclipse
function getdepth( lat, language, color )
{
    var depth = mid[36] / mid[29];
    if (depth < 0.0)
        depth = 1.0 + depth;
    else
        depth = 1.0 - depth;
    var ans = (depth * 100.0).toFixed(2);
    if ( language == "fr" )
        ans = ans.replace(/\./, ',');
    ans += "%";

    var K = (mid[2] * mid[26]) + (mid[3] * mid[27]);
    K *= K;
    K = Math.sqrt((mid[21] * mid[21]) + (K / mid[30]));
    var halfwidth = getWGS84EarthRadiusAtLatitude(lat) * Math.abs(mid[29]) / K;
    var centerlineDist = halfwidth * (1.0 - depth);
    ans += "<br>";
    if ( language == "fr" )
    {
        if (mid[39] == 2)
            ans += '<span style="color: #' + color + ';">P\xE9n\xE9tration ant\xE9-ombre\xA0:\xA0</span>';
        else
            ans += '<span style="color: #' + color + ';">P\xE9n\xE9tration dans l\u2019ombre\xA0:\xA0</span>';
        ans += '<span title="Distance vers la ligne de centralit\xE9">';
    }
    else
    {
        if (mid[39] == 2)
            ans += '<span style="color: #' + color + ';">Antumbral depth\xA0:\xA0</span>';
        else
            ans += '<span style="color: #' + color + ';">Umbral depth\xA0:\xA0</span>';
        ans += '<span title="Distance to the centerline">';
    }
    if (centerlineDist < 1.0)
    {
        centerlineDist *= 1000.0;
        ans += centerlineDist.toFixed(0) + "m";
        if ( language == "en" )
            ans += " (" + (centerlineDist * 3.28084).toFixed(0) + "ft)";
    }
    else
    {
        ans += ((language == "fr") ? (centerlineDist.toFixed(1).replace(/\./, ',')) : centerlineDist.toFixed(1)) + "km";
        if ( language == "en" )
            ans += " (" + (centerlineDist / 1.609344).toFixed(1) + "mi)";
    }
    ans += '</span>';

    return ans;
}

//
// Get the (Ant)Umbral path width
// Entry condition - there is a total or annular eclipse (doesn't work well for non-central eclipses)
function getwidth( lat, language )
{
    var K = (mid[2] * mid[26]) + (mid[3] * mid[27]);
    K *= K;
    K = Math.sqrt((mid[21] * mid[21]) + (K / mid[30]));
    var width = 2.0 * getWGS84EarthRadiusAtLatitude(lat) * Math.abs(mid[29]) / K;
    if (width < 1.0)
    {
        width *= 1000.0;
        var ans = width.toFixed(0) + "m";
        if ( language == "en" )
            ans += " (" + (width * 3.28084).toFixed(0) + "ft)";
    }
    else
    {
        var ans = width.toFixed(1) + "km";
        if ( language == "en" )
            ans += " (" + (width / 1.609344).toFixed(1) + "mi)";
    }
    if ( language == "fr" )
        ans = ans.replace(/\./, ',');

    return ans;
}

//
// WGS84 Earth radius in kilometers at a given geodetic latitude in degrees (the flattening is taken into account)
function getWGS84EarthRadiusAtLatitude( lat )
{
    var phi, tmp, numerator, denominator, radius;

    phi = lat * D2R;
    // Radius for WGS84 ellipsoid (slightly different from IAU 1976)
    numerator = (kEARTH_EQUATORIAL_RADIUS * kEARTH_EQUATORIAL_RADIUS) * Math.cos(phi);
    numerator *= numerator;
    tmp = (kEARTH_POLAR_RADIUS * kEARTH_POLAR_RADIUS) * Math.sin(phi);
    tmp *= tmp;
    numerator += tmp;
    denominator = kEARTH_EQUATORIAL_RADIUS * Math.cos(phi);
    denominator *= denominator;
    tmp = kEARTH_POLAR_RADIUS * Math.sin(phi);
    tmp *= tmp;
    denominator += tmp;
    radius = Math.sqrt(numerator / denominator) / 1000.0;

    return radius;
}

//
// Get the time of greatest eclipse
function getGreatestEclipseInstant( )
{
    var tGE, x, y, xp, yp, n2, hasGreatestEclipse;
    var iter = 0;
    var tau = 1.0;
    var t = 0.0;

    do
    {
        x = elements[6] + (t * (elements[7] + (t * (elements[8] + (elements[9] * t)))));
        y = elements[10] + (t * (elements[11] + (t * (elements[12] + (elements[13] * t)))));
        xp = elements[7] + (t * ((2.0 * elements[8]) + (3.0 * elements[9] * t)));
        yp = elements[11] + (t * ((2.0 * elements[12]) + (3.0 * elements[13] * t)));
        n2 = (xp * xp) + (yp * yp);
        if (n2 != 0.0)
        {
            tau = ((x * xp) + (y * yp)) / n2;	// Correction in time
            t -= tau;
        }

        iter++;
    }
    while ((Math.abs(tau) > 0.000001) && (iter < 20));

    hasGreatestEclipse = ((iter < 20) && (Math.abs(t) <= 3.0));

    if ( hasGreatestEclipse == true )
    {
        var d, V, gammaU, ts, sign;

        tGE = t;

        // In partial eclipses, the maximum magnitude is attained at the point of the surface of the Earth
        // that comes closest to the axis of the shadow. That maximum eclipse will occur on the horizon.
        // Due to the Earth's flattening, the time of closest approach of the shadow axis to the surface of the Earth
        // isn't exactly the same as the time of closest approach to the center of the Earth.
        y = elements[10] + (t * (elements[11] + (t * (elements[12] + (elements[13] * t)))));
        if (y >= 0.0)
            sign = 1.0;
        else
            sign = -1.0;
        d = (elements[14] + (t * (elements[15] + (elements[16] * t)))) * D2R;
        xp = elements[7] + (t * ((2.0 * elements[8]) + (3.0 * elements[9] * t)));
        yp = elements[11] + (t * ((2.0 * elements[12]) + (3.0 * elements[13] * t)));
        n2 = (xp * xp) + (yp * yp);
        V = 0.00669437999014 * Math.cos(d) * Math.cos(d);
        gammaU = sign * Math.sqrt(1.0 - ((V * (xp * xp)) / n2));
        ts = t - ((V * xp * yp) / (gammaU * (Math.sqrt(n2) * n2)));
        tGE = ts;
    }
    else
        tGE = 0.0;

    return tGE;
}

//
// Get the gamma value at maximum eclipse.
// Refer to http://en.wikipedia.org/wiki/Gamma_(eclipse) (0.9972>=|gamma|<1.0260 non central)
function getGamma( )
{
    var tGE = getGreatestEclipseInstant();
    var x = elements[6] + (tGE * (elements[7] + (tGE * (elements[8] + (elements[9] * tGE)))));
    var y = elements[10] + (tGE * (elements[11] + (tGE * (elements[12] + (elements[13] * tGE)))));
    var gamma = Math.sqrt((x * x) + (y * y));
    if ( y < 0.0 )	// Southern hemisphere
        gamma = -gamma;

    return gamma;
}

function isCentralEclipse( )
{
    var gamma = Math.abs(getGamma());
    if (gamma >= 0.9972)
        return false;
    else
        return true;
}

//
// Get the (Ant-)Umbral velocity in km/s
// Entry condition - there is a total or annular eclipse (doesn't work well for non-central eclipses)
function getVelocity( language )
{
    var ans = "";

    if (mid[39] > 1)
    {
        var temp, zeta1;

        var rho1 = Math.sqrt(1.0 - (kELLIPTICITY_SQUARRED * mid[6] * mid[6]));
        var eta1 = mid[3] / rho1;
        var temp = (mid[2] * mid[2]) + (eta1 * eta1);
        if (temp >= 1.0)	// Check for square root of 0 or negative number
            zeta1 = 0.0;
        else
            zeta1 = Math.sqrt(1.0 - temp);
        var sinD1 = mid[5] / rho1;
        var cosD1 = (1.0 - kEARTH_INV_FLATTENING) * mid[6] / rho1;
        var theta = Math.atan2(mid[2], (zeta1 * cosD1) - (eta1 * sinD1));
        if (theta < 0.0)
            theta += 2.0 * Math.PI;
        temp = (1.0 + (kEARTH_INV_F_SQ * mid[5] * mid[5])) * (1.0 - (mid[2] * mid[2])) - (kEARTH_INV_F_SQUARRED * mid[3] * mid[3]);
        if (temp > 0.0)
        {
            var dz = ((1.0 + (kEARTH_INV_F_SQ * mid[5] * mid[5])) * mid[2] * mid[10]) + (kEARTH_INV_F_SQUARRED * mid[3] * mid[11]);
            dz /= Math.sqrt(temp);
            dz += kEARTH_INV_F_SQ * mid[11] * mid[5] * mid[6];
            dz = -dz / (1.0 + (kEARTH_INV_F_SQ * mid[5] * mid[5]));
            var dxi2 = mid[13] * mid[2] * Math.cos(theta) / Math.sin(theta);
            var deta2 = mid[13] * mid[2] * mid[5];
            var dzeta2 = -mid[13] * mid[2] * mid[6];

            // (Ant-)umbra velocity in km/s
            var du = mid[10] - dxi2;
            var dv = mid[11] - deta2;
            var dw = dz - dzeta2;
            var velocity = Math.sqrt((du * du) + (dv * dv) + (dw * dw)) * kEARTH_EQUATORIAL_RADIUS / 3600000.0;

            ans = velocity.toFixed(3) + "km/s";
            if ( language == "fr" )
                ans = ans.replace(/\./, ',');
            else
                ans += " (" + (velocity * 2236.93629).toFixed(0) + " mph)";
        }
    }

    return ans;
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
    var htmlc1 = "";
    var htmlc2 = "";
    var htmlmid = "";
    var htmlc3 = "";
    var htmlc4 = "";
    var htmlEclipse = "";
    var htmlCoverage = "";
    var htmlMagnitude = "";
    var htmlsunrise = "";
    var htmlsunset = "";
    gIFRAMEid = "";
    var partialEvent = false;
    var isEclipse = true;
    var durationOnly = false;

    if ( ( isNaN(lat) ) || ( isNaN(lon) ) )
        return html;
    if ( elv < 0.0 )
        elv = 0.0;

    if ( type == undefined )
        type = "full";

    gSVG_VML_Support = CheckSVG_VML();
    readdata(lat, lon, elv);

console.log(obsvconst);
    getall(language, true);
    var deltaT = getdTValue(1);
    if ( durationOnly == false )
        htmlmid = displaymid(language);
    // Is there an event?

    if (mid[39] > 0)
    {
        var centralEclipse = isCentralEclipse();
        if ( durationOnly == false )
        {
            htmlc1 = displayc1(language);
            htmlc4 = displayc4(language);
        }
        // Is there a total/annular event?
        if (mid[39] > 1)
        {
            if ( durationOnly == false )
            {
                htmlc2 = displayc2(language);
                htmlc3 = displayc3(language);
            }
            // Is the Sun below the horizon for the entire duration of the event?
            if ((c1[32] <= gRefractionHeight) && (mid[32] <= gRefractionHeight) && (c4[32] <= gRefractionHeight))	// Cf PSE 2019 (limit case where obscuration can be under the horizon)
            {
                isEclipse = false;
                gNoEclipse = true;
                var underHorizonAlt = (mid[32] * kR2D).toFixed(1);
                if ( language == "fr" )
                    htmlEclipse += "(AUCUNE ECLIPSE DE SOLEIL VISIBLE)<br />[" + underHorizonAlt.replace(/\./, ',') + "&deg; sous l\u2019horizon]";
                else
                    htmlEclipse += "(NO VISIBLE SOLAR ECLIPSE)<br />[" + underHorizonAlt + "&deg; under the horizon]";
            }
            else // ... or is the Sun above the horizon for at least some of the event?
            {
                gNoEclipse = false;
                // Is the Sun below the horizon for just the total/annular event?
                if ((c2[32] <= gRefractionHeight) && (c3[32] <= gRefractionHeight))
                {
                    partialEvent = true;
                    if ( language == "fr" )
                        htmlEclipse += "(\xE9clipse partielle de soleil)";
                    else
                        htmlEclipse += "(partial solar eclipse)";
                    if ( durationOnly == false )
                    {
                        if ( language == "fr" )
                        {
                            if (mid[39] == 2)
                                htmlCoverage = "P\xE9n\xE9tration ant\xE9-ombre\xA0:\xA0" + "???";
                            else
                                htmlCoverage = "P\xE9n\xE9tration dans l\u2019ombre\xA0:\xA0" + "???";
                            htmlCoverage += "<br />Degr\xE9 d\u2019obscurit\xE9\xA0:\xA0" + "???";
                        }
                        else
                        {
                            if (mid[39] == 2)
                                htmlCoverage = "Antumbral depth\xA0:\xA0" + "???";
                            else
                                htmlCoverage = "Umbral depth\xA0:\xA0" + "???";
                            htmlCoverage += "<br />Obscuration\xA0:\xA0" + "???";
                        }
                    }
                }
                else // ... or is the Sun above the horizon for at least some of the total/annular event?
                {
                    // Is the Sun above the horizon for the entire annular/total event?
                    if ((c2[32] > gRefractionHeight) && (c3[32] > gRefractionHeight))
                    {
                        htmlEclipse += getduration(language);
                        if ( durationOnly == true )
                            htmlEclipse += "<br />";
                        htmlEclipse += " (";
                        // Is it an annular event?
                        if (mid[39] == 2)
                        {
                            if ( language == "fr" )
                                htmlEclipse += "\xE9clipse annulaire de soleil";
                            else
                                htmlEclipse += "annular solar eclipse";
                        }
                        else // ... or is it a total event?
                        {
                            if ( language == "fr" )
                            {
                                if ( ( mid[37] >= 1.0 ) && ( mid[37] <= 1.00012 ) )
                                    htmlEclipse += "\xE9clipse totale perl\xE9e de soleil";
                                else
                                    htmlEclipse += "\xE9clipse totale de soleil";
                            }
                            else
                            {
                                if ( ( mid[37] >= 1.0 ) && ( mid[37] <= 1.00012 ) )
                                    htmlEclipse += "beaded total solar eclipse";
                                else
                                    htmlEclipse += "total solar eclipse";
                            }
                        }
                        htmlEclipse += ")";
                        if ( durationOnly == false )
                        {
                            htmlEclipse += '<div id="duration_lc" class="EclipseCenter"></div>';
                            if ( language == "fr" )
                            {
                                if (mid[39] == 2)
                                    htmlCoverage = "P\xE9n\xE9tration ant\xE9-ombre\xA0:\xA0" + getdepth(lat, language, "FDF3D0");
                                else
                                    htmlCoverage = "P\xE9n\xE9tration dans l\u2019ombre\xA0:\xA0" + getdepth(lat, language, "FDF3D0");
                                if (centralEclipse == true)
                                    htmlCoverage += "<br />Largeur du trac\xE9\xA0:\xA0" + getwidth(lat, language);
                                else
                                    htmlCoverage += "<br />Non centrale";
                                htmlCoverage += "<br />Degr\xE9 d\u2019obscurit\xE9\xA0:\xA0" + getcoverage(language);
                            }
                            else
                            {
                                if (mid[39] == 2)
                                    htmlCoverage = "Antumbral depth\xA0:\xA0" + getdepth(lat, language, "FDF3D0");
                                else
                                    htmlCoverage = "Umbral depth\xA0:\xA0" + getdepth(lat, language, "FDF3D0");
                                if (centralEclipse == true)
                                    htmlCoverage += "<br />Path width\xA0:\xA0" + getwidth(lat, language);
                                else
                                    htmlCoverage += "<br />Non-central";
                                htmlCoverage += "<br />Obscuration\xA0:\xA0" + getcoverage(language);
                            }
                        }
                    }
                    else // ... or is the Sun below the horizon for at least some of the annular/total event
                    {
                        htmlEclipse += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;???&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        // Is the Sun above the horizon at C2 or C3? (the obscuration remains constant during a total/annular event)
                        if ( language == "fr" )
                            htmlCoverage = "Degr\xE9 d\u2019obscurit\xE9\xA0:\xA0";
                        else
                            htmlCoverage = "Obscuration\xA0:\xA0";
                        if ((c2[32] > gRefractionHeight) || (c3[32] > gRefractionHeight))
                            htmlCoverage += getcoverage(language);
                        else
                            htmlCoverage += "???";
                    }
                }
            }
        }
        else // ... or is it just a partial event?
        {
            // Is the Sun below the horizon for the entire event?
            if ((c1[32] <= gRefractionHeight) && (mid[32] <= gRefractionHeight) && (c4[32] <= gRefractionHeight))	// Cf PSE 2019 (limit case where obscuration can be under the horizon)
            {
                isEclipse = false;
                gNoEclipse = true;
                if ( language == "fr" )
                    htmlEclipse += "(AUCUNE ECLIPSE DE SOLEIL VISIBLE)";
                else
                    htmlEclipse += "(NO VISIBLE SOLAR ECLIPSE)";
            }
            else // ... or is the Sun above the horizon for at least some of the event?
            {
                partialEvent = true;
                gNoEclipse = false;
                if ( language == "fr" )
                    htmlEclipse += "(\xE9clipse partielle de soleil)";
                else
                    htmlEclipse += "(partial solar eclipse)";
                if ( durationOnly == false )
                {
                    if ( language == "fr" )
                        htmlCoverage = "Degr\xE9 d\u2019obscurit\xE9\xA0:\xA0";
                    else
                        htmlCoverage = "Obscuration\xA0:\xA0";
                    // Is the Sun below the horizon at maximum eclipse?
                    if (mid[32] <= gRefractionHeight)
                        htmlCoverage += "???";
                    else // ... or is the Sun above the horizon at maximum eclipse?
                        htmlCoverage += getcoverage(language);
                }
            }
        }
    }
    else // ... or is there no event at all?
    {
        isEclipse = false;
        gNoEclipse = true;
        if ( language == "fr" )
            htmlEclipse += "(AUCUNE ECLIPSE DE SOLEIL)";
        else
            htmlEclipse += "(NO SOLAR ECLIPSE)";
    }

    if ( isEclipse == true )
    {
        if ( type == "full" )
        {
            /*      if (mid[39] > 1)
             html = '<div style="width: ' + (( language == "fr" ) ? '495' : ((mid[39] >= 2) ? '510' : '475')) + 'px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
             else
             html = '<div style="width: ' + (( language == "fr" ) ? '450' : '430') + 'px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';*/
            html = '<div style="width: 100%; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
        }
        else
        {
            if ( document.getElementById("chbDetails") )
            {
                if ( document.getElementById("chbDetails").checked == true )
                {
                    /*          if (mid[39] > 1)
                     html = '<div style="width: ' + (( language == "fr" ) ? '495' : ((mid[39] >= 2) ? '510' : '475')) + 'px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
                     else
                     html = '<div style="width: ' + (( language == "fr" ) ? '450' : '430') + 'px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';*/
                    html = '<div style="width: 100%; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
                }
                else
                {
                    durationOnly = true;
//          html = '<div style="width: 200px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
                    html = '<div style="width: 100%; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
                }
            }
            else
            {
                /*        if (mid[39] > 1)
                 html = '<div style="width: ' + (( language == "fr" ) ? '495' : ((mid[39] >= 2) ? '510' : '475')) + 'px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
                 else
                 html = '<div style="width: ' + (( language == "fr" ) ? '450' : '430') + 'px; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';*/
                html = '<div style="width: 100%; font-size: 7pt; font-weight: bold; text-align: left; background-color: #FDF3D0;">';
            }
        }
        html += '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
        html += '<tr>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + dd2dms(lat, 1, language) + '</td>';
        html += '<td align="center" class="EclipseCenter" nowrap="nowrap">&nbsp;&nbsp;&lt;&mdash;&gt;&nbsp;&nbsp;</td>';
        html += '<td align="right" class="EclipseLatLon" nowrap="nowrap">' + (( language == "fr" ) ? lat.toFixed(5).replace(/\./, ',') : lat.toFixed(5)) + '&deg;</td>';
        html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter">&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        if ( durationOnly == false )
        {
            html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter" nowrap="nowrap">';
            html += htmlEclipse;
            html += '</td>';
            html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter">&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        }
        if ( language == "fr" )
            html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter"><a href="javascript:openGMWindow(\'../solar_eclipses/xSE_GoogleMap3_Help.html\',\'\');" class="EclipseHelp" title="Aide &laquo;Cartographie Google&raquo;">Aide</a></td>';
        else
            html += '<td rowspan="' + (( obsvconst[2] == 0.0 ) ? '2' : '3') + '" align="center" class="EclipseCenter"><a href="javascript:openGMWindow(\'../solar_eclipses/xSE_GoogleMap3_Help.html\',\'\');" class="EclipseHelp" title="&quot;Google Map&quot; Help">Help</a></td>';
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
        if ( durationOnly == true )
        {
            html += '<tr>';
            html += '<td colspan="3" align="center" class="EclipseCenter">';
            html += htmlEclipse;
            html += '</td>';
            html += '<td align="center" class="EclipseCenter">&nbsp;&nbsp;&nbsp;&nbsp;</td>';
            html += '<td align="center" class="EclipseCenter">&nbsp;</td>';
            html += '</tr>';
        }
        html += '</table>';

        if ( durationOnly == false )
        {
            var htmlVelocity = getVelocity(language);
            if ( language == "fr" )
            {
                htmlMagnitude = "Grandeur au maximum\xA0:\xA0" + mid[37].toFixed(5).replace(/\./, ',');
                htmlMagnitude += "<br />Rapport Lune/Soleil\xA0:\xA0" + mid[38].toFixed(5).replace(/\./, ',');
                if (htmlVelocity != "")
                {
                    if (mid[39] == 2)
                        htmlMagnitude += "<br />V\xE9locit\xE9 de l\u2019ant\xE9-ombre\xA0:\xA0" + htmlVelocity;
                    else
                        htmlMagnitude += "<br />V\xE9locit\xE9 de l\u2019ombre\xA0:\xA0" + htmlVelocity;
                }
            }
            else
            {
                htmlMagnitude = "Magnitude at maximum\xA0:\xA0" + mid[37].toFixed(5);
                htmlMagnitude += "<br />Moon/Sun size ratio\xA0:\xA0" + mid[38].toFixed(5);
                if (htmlVelocity != "")
                {
                    if (mid[39] == 2)
                        htmlMagnitude += "<br />Antumb. vel.\xA0:\xA0" + htmlVelocity;
                    else
                        htmlMagnitude += "<br />Umbral vel.\xA0:\xA0" + htmlVelocity;
                }
            }

            if ( ( htmlCoverage != "" ) || ( htmlMagnitude != "" ) )
            {
                html += '<table border="0" cellspacing="1" width="100%">';
                html += '<tr>';
                if ( htmlCoverage != "" )
                {
                    html += '<td class="EclipseLeft" title="' + (( language == "fr" ) ? "Degr&eacute; d&rsquo;obscurit&eacute; au maximum de l&rsquo;&eacute;clipse" : "Obscuration at maximum eclipse" ) + '" nowrap="nowrap">';
                    html += htmlCoverage;
                }
                else
                    html += '<td class="Eclipse" nowrap="nowrap">';
                html += '</td>';
                html += '<td width="2">&nbsp;</td>';
                html += '<td class="Eclipse" style="width: 102px; height: 42px;" nowrap="nowrap">';
                if ( gSupportHTML5Canvas )
                {
                    html += '<div id="canvas_container" style="text-align: center; border: solid 1px lightgrey; width: 100px; height: 40px;">';
                    html += '<canvas id="SE_diagram" width="100" height="40" style="width: 100px; height: 40px;"></canvas>';
                    html += '<div id="circum_label" align="left" style="position: relative; left: 5px; top: -40px; text-align: left; color: #FF6600; height: 0px; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 8pt; font-weight: bold;"></div>';
                    html += '</div>';
                }
                else
                {
                    if ( ! isIE ) // Use SVG
                    {
                        // Doesn't work well in Firefox because of a long-standing bug with iframe!!!
                        if ( isFirefox )
                        {
                            gIFRAMEid = "SE_diagram" + gIFRAMEindex;
                            if ( (gIFRAMEindex % 2) == 0 )
                                html += '<iframe src="SolarEclipse_Diagram.xhtml" name="' + gIFRAMEid + '" id="' + gIFRAMEid + '" width="100" height="40" align="middle" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="true" style="overflow: hidden; border: none;">' + (( language == "fr" ) ? 'Sch&#233;ma Eclipse' : 'Eclipse Diagram' ) + '</iframe>';
                            else
                                html += '<iframe src="SolarEclipse_Diagram2.xhtml" name="' + gIFRAMEid + '" id="' + gIFRAMEid + '" width="100" height="40" align="middle" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="true" style="overflow: hidden; border: none;">' + (( language == "fr" ) ? 'Sch&#233;ma Eclipse' : 'Eclipse Diagram' ) + '</iframe>';
                            gIFRAMEindex++;
                            html += '<div id="circum_label" align="left" style="position: relative; left: 5px; top: -38px; display: block; text-align: left; color: #FF6600; height: 0px; font-size: 8pt;"></div>';
                        }
                        else
                        {
                            gIFRAMEid = "SE_diagram";
                            html += '<iframe src="SolarEclipse_Diagram.xhtml" name="SE_diagram" id="SE_diagram" width="102" height="42" align="middle" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="true" style="overflow: hidden; border: none;">' + (( language == "fr" ) ? 'Sch&#233;ma Eclipse' : 'Eclipse Diagram' ) + '</iframe>';
                        }
                    }
                    else // Use VML
                    {
                        html += '<div id="eclipse_diagram" align="center" style="position: relative; left: 0px; top: 0px; text-align: center; vertical-align: top; width: 100px; height: 40px; clip: rect(0px 100px 40px 0px); overflow: hidden;">';
                        html += drawDiagram('mid', true, language);
                        html += '</div>';
                    }
                }
                html += '</td>';
                html += '<td width="2">&nbsp;</td>';
                html += '<td class="Eclipse" nowrap="nowrap">';
                if ( htmlMagnitude != "" )
                    html += htmlMagnitude;
                html += '</td>';
                html += '</tr>';
                html += '</table>';
            }

            /*      if (mid[39] > 1)
             html += '<div align="center" style="width: ' + (( language == "fr" ) ? '495' : ((mid[39] >= 2) ? '510' : '475')) + 'px; font-size: 7pt; font-weight: bold;"><center>';
             else
             html += '<div align="center" style="width: ' + (( language == "fr" ) ? '450' : '430') + 'px; font-size: 7pt; font-weight: bold;"><center>';*/
            html += '<div align="center" style="width: 100%; font-size: 7pt; font-weight: bold;"><center>';
            html += '<table border="0" cellspacing="1" width="100%">';
            html += '<tr align="center" bgcolor="#DDAD08">';
            if (mid[39] > 1)
            {
                var numDate = new Object();
                getnumUTdate(mid, numDate);
            }
            var TZ = (-obsvconst[3]).toFixed(1);
            if (obsvconst[3] < 0.0)
                TZ = "+" + TZ;
            if ( language == "fr" )
            {
                if (mid[39] > 1)
                    html += '<th class="Eclipse" nowrap="nowrap">Phase&nbsp;(&Delta;T=' + deltaT.replace(/\./, ',') + 's' + ((elv > 0) ? ';&nbsp;alt.=' + elv.toFixed(0) + 'm' : '') +')</th><th class="Eclipse">Date</th><th class="Eclipse" nowrap="nowrap">Heure&nbsp;(' + ((obsvconst[3] == 0.0) ? 'TU' : TZ.replace(/\./, ',')) + ')</th><th class="Eclipse">Alt</th><th class="Eclipse">Azi</th><th class="Eclipse">P</th><th class="Eclipse">V</th>' + (( mid[39] > 1 ) ? '<th class="Eclipse"><a href="../../php/php5/WattsChartBailyBeads.php?DF=15&BB=3.0&RM=' + mid[44] + '&ZM=' + mid[47] + '&RS=' + mid[43] + '&LibL=' + mid[48].toFixed(2) + '&LibB=' + mid[49].toFixed(2) + '&MN=' + mid[50].toFixed(2) + '&PA2=' + (rev(c2[31] * R2D).toFixed(2)) + '&PA3=' + (rev(c3[31] * R2D).toFixed(2)) + '&Dur=' + getdurationseconds() + '&Zen=' + (mid[33] * R2D).toFixed(2) + '&SN=' + mid[51].toFixed(2) + '&Lat=' + (obsvconst[0] * R2D).toFixed(7) + '&Lng=' + (-obsvconst[1] * R2D).toFixed(7) + '&Elv=' + (obsvconst[2].toFixed(2)) + '&Y=' + numDate.year + '&M=' + numDate.month + '&D=' + numDate.day + '&T=' + ( (mid[39] == 3) ? 'T' : 'A' ) + '&dT=' + getdTValue(2) + '&Mes=' + gMes + '&VSOP=' + gVSOP + '&Lang=fr" class="watts" target="_blank" title="Diagramme de ' + (( Math.abs(mid[49]) > 1.6 ) ? 'Watts' : 'Kaguya') + ' avec les corrections dues au limbe lunaire...">CL</a></th>' : '');
                else
                    html += '<th class="Eclipse" nowrap="nowrap">Phase&nbsp;(&Delta;T=' + deltaT.replace(/\./, ',') + 's' + ((elv > 0) ? ';&nbsp;alt.=' + elv.toFixed(0) + 'm' : '') +')</th><th class="Eclipse">Date</th><th class="Eclipse" nowrap="nowrap">Heure&nbsp;(' + ((obsvconst[3] == 0.0) ? 'TU' : TZ.replace(/\./, ',')) + ')</th><th class="Eclipse">Alt</th><th class="Eclipse">Azi</th><th class="Eclipse">P</th><th class="Eclipse">V</th>' + (( mid[39] > 1 ) ? '<th class="Eclipse">CL</th>' : '');
            }
            else
            {
                if (mid[39] > 1)
                    html += '<th class="Eclipse" nowrap="nowrap">Event&nbsp;(&Delta;T=' + deltaT + 's' + ((elv > 0) ? ';&nbsp;alt.=' + elv.toFixed(0) + 'm' : '') +')</th><th class="Eclipse">Date</th><th class="Eclipse" nowrap="nowrap">Time&nbsp;(' + ((obsvconst[3] == 0.0) ? 'UT' : TZ.replace(/\./, ',')) + ')</th><th class="Eclipse">Alt</th><th class="Eclipse">Azi</th><th class="Eclipse">P</th><th class="Eclipse">V</th>' + (( mid[39] > 1 ) ? '<th class="Eclipse"><a href="../../../php/php5/WattsChartBailyBeads.php?DF=15&BB=3.0&RM=' + mid[44] + '&ZM=' + mid[47] + '&RS=' + mid[43] + '&LibL=' + mid[48].toFixed(2) + '&LibB=' + mid[49].toFixed(2) + '&MN=' + mid[50].toFixed(2) + '&PA2=' + (rev(c2[31] * R2D).toFixed(2)) + '&PA3=' + (rev(c3[31] * R2D).toFixed(2)) + '&Dur=' + getdurationseconds() + '&Zen=' + (mid[33] * R2D).toFixed(2) + '&SN=' + mid[51].toFixed(2) + '&Lat=' + (obsvconst[0] * R2D).toFixed(7) + '&Lng=' + (-obsvconst[1] * R2D).toFixed(7) + '&Elv=' + (obsvconst[2].toFixed(2)) + '&Y=' + numDate.year + '&M=' + numDate.month + '&D=' + numDate.day + '&T=' + ( (mid[39] == 3) ? 'T' : 'A' ) + '&dT=' + getdTValue(2) + '&Mes=' + gMes + '&VSOP=' + gVSOP + '&Lang=en" class="watts" target="_blank" title="' + (( Math.abs(mid[49]) > 1.6 ) ? 'Watts' : 'Kaguya') + ' chart with the lunar limb contact corrections...">LC</a></th>' : '');
                else
                    html += '<th class="Eclipse" nowrap="nowrap">Event&nbsp;(&Delta;T=' + deltaT + 's' + ((elv > 0) ? ';&nbsp;alt.=' + elv.toFixed(0) + 'm' : '') +')</th><th class="Eclipse">Date</th><th class="Eclipse" nowrap="nowrap">Time&nbsp;(' + ((obsvconst[3] == 0.0) ? 'UT' : TZ.replace(/\./, ',')) + ')</th><th class="Eclipse">Alt</th><th class="Eclipse">Azi</th><th class="Eclipse">P</th><th class="Eclipse">V</th>' + (( mid[39] > 1 ) ? '<th class="Eclipse">LC</th>' : '');
            }
            html += "</tr>";

            // Look for sunrise/sunset during/around the eclipse
            getsunrise(sunrise);
            if ( sunrise[1] != mid[1] )
                htmlsunrise = displaysunrise(language);
            getsunset(sunset);
            if ( sunset[1] != mid[1] )
                htmlsunset = displaysunset(language);

            if ( ( htmlsunrise != "" ) && ( c1[1] >= sunrise[1] ) )
                html += htmlsunrise;
            html += htmlc1;
            if ( partialEvent == false )
            {
                if ( ( htmlsunrise != "" ) && ( c1[1] < sunrise[1] ) && ( c2[1] >= sunrise[1] ) )
                    html += htmlsunrise;
                if ( ( htmlsunset != "" ) && ( c1[1] < sunset[1] ) && ( c2[1] >= sunset[1] ) )
                    html += htmlsunset;
                html += htmlc2;
                if ( ( htmlsunrise != "" ) && ( c2[1] < sunrise[1] ) && ( mid[1] >= sunrise[1] ) )
                    html += htmlsunrise;
                if ( ( htmlsunset != "" ) && ( c2[1] < sunset[1] ) && ( mid[1] >= sunset[1] ) )
                    html += htmlsunset;
            }
            else
            {
                if ( ( htmlsunrise != "" ) && ( c1[1] < sunrise[1] ) && ( mid[1] >= sunrise[1] ) )
                    html += htmlsunrise;
                if ( ( htmlsunset != "" ) && ( c1[1] < sunset[1] ) && ( mid[1] >= sunset[1] ) )
                    html += htmlsunset;
            }
            html += htmlmid;
            if ( partialEvent == false )
            {
                if ( ( htmlsunrise != "" ) && ( mid[1] < sunrise[1] ) && ( c3[1] >= sunrise[1] ) )
                    html += htmlsunrise;
                if ( ( htmlsunset != "" ) && ( c3[1] > sunset[1] ) && ( mid[1] <= sunset[1] ) )
                    html += htmlsunset;
                html += htmlc3;
                if ( ( htmlsunrise != "" ) && ( c3[1] < sunrise[1] ) && ( c4[1] >= sunrise[1] ) )
                    html += htmlsunrise;
                if ( ( htmlsunset != "" ) && ( c3[1] < sunset[1] ) && ( c4[1] >= sunset[1] ) )
                    html += htmlsunset;
            }
            else
            {
                if ( ( htmlsunrise != "" ) && ( mid[1] < sunrise[1] ) && ( c4[1] >= sunrise[1] ) )
                    html += htmlsunrise;
                if ( ( htmlsunset != "" ) && ( c4[1] > sunset[1] ) && ( mid[1] <= sunset[1] ) )
                    html += htmlsunset;
            }
            html += htmlc4;
            if ( ( htmlsunset != "" ) && ( c4[1] <= sunset[1] ) )
                html += htmlsunset;
            html += '</table></center></div>';
        }

        if ( typeof daynight !== "undefined" )
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

    shadowOutlineLowAccuracy(mid[1]);

    return html;
}

//
// Re-calculate for geolocation
function recal_geo( language, lat, lon, elv, speed, heading )
{
    var html = "";
    var htmlEclipse = "";
    var htmlCoverage = "";
    var htmlMagnitude = "";
    var partialEvent = false;
    var isEclipse = true;

    if ( ( isNaN(lat) ) || ( isNaN(lon) ) )
        return html;
    if ( ( isNaN(elv) ) || ( elv == null ) )
        elv = 0.0;

    readdata_geo(lat, lon, elv);
    getall(language, false);
    var deltaT = getdTValue(1);
    // Is there an event?
    if (mid[39] > 0)
    {
        var centralEclipse = isCentralEclipse();
        // Is there a total/annular event?
        if (mid[39] > 1)
        {
            // Is the Sun below the horizon for the entire duration of the event?
            if ((c1[32] <= gRefractionHeight) && (mid[32] <= gRefractionHeight) && (c4[32] <= gRefractionHeight))	// Cf PSE 2019 (limit case where obscuration can be under the horizon)
            {
                isEclipse = false;
                var underHorizonAlt = (mid[32] * kR2D).toFixed(1);
                if ( language == "fr" )
                    htmlEclipse += "AUCUNE ECLIPSE DE SOLEIL VISIBLE<br />[" + underHorizonAlt.replace(/\./, ',') + "&deg; sous l\u2019horizon]";
                else
                    htmlEclipse += "NO VISIBLE SOLAR ECLIPSE<br />[" + underHorizonAlt + "&deg; under the horizon]";
            }
            else // ... or is the Sun above the horizon for at least some of the event?
            {
                // Is the Sun below the horizon for just the total/annular event?
                if ((c2[32] <= gRefractionHeight) && (c3[32] <= gRefractionHeight))
                {
                    partialEvent = true;
                    if ( language == "fr" )
                    {
                        htmlEclipse += "Partielle";
                        if (mid[39] == 2)
                            htmlCoverage = "P\xE9n\xE9tration ant\xE9-ombre:\xA0" + "???";
                        else
                            htmlCoverage = "P\xE9n\xE9tration dans l\u2019ombre:\xA0" + "???";
                        htmlCoverage += "<br />Degr\xE9 d\u2019obscurit\xE9:\xA0" + "???";
                    }
                    else
                    {
                        htmlEclipse += "Partial";
                        if (mid[39] == 2)
                            htmlCoverage = "Antumbral depth:\xA0" + "???";
                        else
                            htmlCoverage = "Umbral depth:\xA0" + "???";
                        htmlCoverage += "<br />Obscuration:\xA0" + "???";
                    }
                }
                else // ... or is the Sun above the horizon for at least some of the total/annular event?
                {
                    // Is the Sun above the horizon for the entire annular/total event?
                    if ((c2[32] > gRefractionHeight) && (c3[32] > gRefractionHeight))
                    {
                        // Is it an annular event?
                        if (mid[39] == 2)
                        {
                            if ( language == "fr" )
                                htmlEclipse += "Annulaire";
                            else
                                htmlEclipse += "Annular";
                        }
                        else // ... or is it a total event?
                        {
                            if ( language == "fr" )
                            {
                                if ( ( mid[37] >= 1.0 ) && ( mid[37] <= 1.00012 ) )
                                    htmlEclipse += "Totale perl\xE9e";
                                else
                                    htmlEclipse += "Totale";
                            }
                            else
                            {
                                if ( ( mid[37] >= 1.0 ) && ( mid[37] <= 1.00012 ) )
                                    htmlEclipse += "Beaded total";
                                else
                                    htmlEclipse += "Total";
                            }
                        }
                        htmlEclipse += " (" + getduration(language) + ( ( language == "fr" ) ? " non corrig\xE9e)" : " uncorrected)" );
                        if ( language == "fr" )
                        {
                            if (mid[39] == 2)
                                htmlCoverage = "P\xE9n\xE9tration ant\xE9-ombre:\xA0" + getdepth(lat, language, "FFFFFF");
                            else
                                htmlCoverage = "P\xE9n\xE9tration dans l\u2019ombre:\xA0" + getdepth(lat, language, "FFFFFF");
                            if (centralEclipse == true)
                                htmlCoverage += "<br />Largeur du trac\xE9:\xA0" + getwidth(lat, language);
                            else
                                htmlCoverage += "<br />Non centrale";
                            htmlCoverage += "<br />Degr\xE9 d\u2019obscurit\xE9:\xA0" + getcoverage(language);
                        }
                        else
                        {
                            if (mid[39] == 2)
                                htmlCoverage = "Antumbral depth:\xA0" + getdepth(lat, language, "FFFFFF");
                            else
                                htmlCoverage = "Umbral depth:\xA0" + getdepth(lat, language, "FFFFFF");
                            if (centralEclipse == true)
                                htmlCoverage += "<br />Path width:\xA0" + getwidth(lat, language);
                            else
                                htmlCoverage += "<br />Non-central";
                            htmlCoverage += "<br />Obscuration:\xA0" + getcoverage(language);
                        }
                    }
                    else // ... or is the Sun below the horizon for at least some of the annular/total event
                    {
                        htmlEclipse += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;???&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        // Is the Sun above the horizon at C2 or C3? (the obscuration remains constant during a total/annular event)
                        if ( language == "fr" )
                            htmlCoverage = "Degr\xE9 d\u2019obscurit\xE9:\xA0";
                        else
                            htmlCoverage = "Obscuration:\xA0";
                        if ((c2[32] > gRefractionHeight) || (c3[32] > gRefractionHeight))
                            htmlCoverage += getcoverage(language);
                        else
                            htmlCoverage += "???";
                    }
                }
            }
        }
        else // ... or is it just a partial event?
        {
            // Is the Sun below the horizon for the entire event?
            if ((c1[32] <= gRefractionHeight) && (mid[32] <= gRefractionHeight) && (c4[32] <= gRefractionHeight))	// Cf PSE 2019 (limit case where obscuration can be under the horizon)
            {
                isEclipse = false;
                if ( language == "fr" )
                    htmlEclipse += "AUCUNE ECLIPSE DE SOLEIL VISIBLE";
                else
                    htmlEclipse += "NO VISIBLE SOLAR ECLIPSE";
            }
            else // ... or is the Sun above the horizon for at least some of the event?
            {
                partialEvent = true;
                if ( language == "fr" )
                    htmlEclipse += "Partielle";
                else
                    htmlEclipse += "Partial";
                if ( language == "fr" )
                    htmlCoverage = "Degr\xE9 d\u2019obscurit\xE9:\xA0";
                else
                    htmlCoverage = "Obscuration:\xA0";
                // Is the Sun below the horizon at maximum eclipse?
                if (mid[32] <= gRefractionHeight)
                    htmlCoverage += "???";
                else // ... or is the Sun above the horizon at maximum eclipse?
                    htmlCoverage += getcoverage(language);
            }
        }
    }
    else // ... or is there no event at all?
    {
        isEclipse = false;
        if ( language == "fr" )
            htmlEclipse += "AUCUNE ECLIPSE DE SOLEIL";
        else
            htmlEclipse += "NO SOLAR ECLIPSE";
    }

    var navUA = navigator.userAgent.toLowerCase();
    var isPhoneTablet = ( (navUA.indexOf("iphone") != -1) || (navUA.indexOf("ipad") != -1) || (navUA.indexOf("ipod") != -1) || (navUA.indexOf("android") != -1) || (navUA.indexOf("bb10") != -1) || (navUA.indexOf("iemobile") != -1) || (navUA.indexOf("mobile") != -1) || (navUA.indexOf("tablet") != -1) );
    if ( isEclipse == true )
    {
        var true_alt = elevationRefraction(mid[32] * R2D);
        if ( language == "fr" )
        {
            htmlMagnitude = "Grandeur au max.:\xA0" + mid[37].toFixed(5).replace(/\./, ',');
//      htmlMagnitude += "<br />Rapport Lune/Soleil:\xA0" + mid[38].toFixed(5).replace(/\./, ',');
            htmlMagnitude += "<br />Soleil au max.:\xA0" + true_alt.toFixed(1).replace(/\./, ',') + "&deg; Azi.\xA0:\xA0" + getazi(mid, language) + "&deg;";
            htmlMagnitude += "<br />Maximum:\xA0" + getdate(mid, language) + " &agrave; " + gettimemiddle(mid) + "TU";
        }
        else
        {
            htmlMagnitude = "Magnitude at max.:\xA0" + mid[37].toFixed(5);
//      htmlMagnitude += "<br />Moon/Sun size ratio:\xA0" + mid[38].toFixed(5);
            htmlMagnitude += "<br />Sun at max.:\xA0" + true_alt.toFixed(1) + "&deg; Az.\xA0:\xA0" + getazi(mid, language) + "&deg;";
            htmlMagnitude += "<br />Maximum:\xA0" + getdate(mid, language) + " at " + gettimemiddle(mid) + "UT";
        }

        /*    var Dist = 0;
         if (location.search.length > 1)
         {
         var argstr = location.search.substring(1, location.search.length);
         var args = argstr.split("&");
         for (var i = 0; i < args.length; i++)
         {
         if ( args[i].substring(0, 5) == "Dist=" )
         eval(unescape(args[i]));
         }
         }*/

//    html = '<div id="geolocationdata" style="font-size: ' + (( isPhoneTablet == false ) ? '7' : '6') + 'pt; font-weight: bold; text-align: left; letter-spacing: -0.5px; white-space: nowrap;" nowrap="nowrap">';
        html = '<div id="geolocationdata" style="font-size: ' + (( isPhoneTablet == false ) ? '7' : '6') + 'pt; font-weight: bold; text-align: left; white-space: nowrap;" nowrap="nowrap">';
        html += htmlEclipse;
//    if ( Dist == 0 )
//    {
        if ( htmlCoverage != "" )
            html += "<br />" + htmlCoverage;
        if ( htmlMagnitude != "" )
            html += "<br />" + htmlMagnitude;

        if (html != "")
            html += nearestCenterlineDistance(language, lat * D2R, lon * D2R);
        if ( ( !isNaN(speed) ) && ( speed != null ) )
        {
            if ( speed > 0.0 )
            {
                if ( language == "fr" )
                    html += "<br />D&eacute;placement &agrave; " + (speed * 3.6).toFixed(0) + " km/h";
                else
                    html += "<br />Moving at " + (speed * 2.23694).toFixed(0) + " mph (" + (speed * 3.6).toFixed(0) + " kph)";
                if ( ( !isNaN(heading) ) && ( heading != null ) )
                {
                    var hdg = heading;

                    if (hdg < 0.0)
                        hdg += 360.0;
                    html += " " + headingDirection(language, hdg);
                }
                if ( elv > 0.0 )
                {
                    if ( language == "fr" )
                        html += "<br />A une altitude de " + elv.toFixed(0) + " m (" + (elv * 0.3048).toFixed(0) + " ft)";
                    else
                        html += "<br />At an elevation of " + (elv * 3.28084).toFixed(0) + " ft (" + elv.toFixed(0) + " m)";
                }
            }
//      }
        }
        html += '</div>';
    }
    else // No eclipse
        html = '<p style="font-size: ' + (( isPhoneTablet == false ) ? '7' : '6') + 'pt; font-weight: bold; text-align: center; letter-spacing: -0.5px; white-space: nowrap;">' + htmlEclipse + '</p>';

    return html;
}

function truncate( x )
{
    return ( x >= 0.0 ) ? Math.floor(x) : Math.ceil(x);
}

function eclipseCalcPreload( )
{
    return;
}

function bringToFront( id )
{
    var old_element = frames[gIFRAMEid].document.getElementById(id);
    if ( old_element )
        old_element.parentNode.appendChild(old_element);
}

//
// Give enough time for the iframe to load (up to a few seconds) and workaround a long-standing Firefox bug
function initSVGDiagram( language )
{
    if ( ! isIE ) // Use SVG
    {
        if ( gIFRAMEid != "" )
        {
            var svg_dg = null;

            var iframe_ref = frames[gIFRAMEid];
            if ( iframe_ref != null )
            {
                var iframe_doc = iframe_ref.document;
                if ( iframe_doc != null )
                {
                    svg_dg = iframe_doc.getElementById("svgdiagram");
                    if ( svg_dg != null )
                    {
                        gNbLoadiframe = 0;
                        drawDiagram("mid", false, language);
                    }
                }
            }

            if ( svg_dg == null )
            {
                if ( gNbLoadiframe < 50 ) // Leave up to 5 seconds for the iframe to load
                {
                    gNbLoadiframe++;
                    setTimeout("initSVGDiagram(" + (( language == "fr" ) ? "'fr'" : "'en'") + ")", 100);
                }
            }
        }
    }
}

//
// Solve an IE bug when the sun is under the horizon and the ground is not drawn the first time
function initVMLDiagram( language )
{
    if ( isIE ) // Use VML
    {
        setTimeout("drawDiagram('mid', false, " + (( language == "fr" ) ? "'fr'" : "'en'") + ")", 200);
    }
}

function drawDiagram( event, inline, language )
{
    var scx, scy, srd, mcx, mcy, mrd, sky_color, moon_color, angle, angle_ns;
    var labelHtml = "";

    if ( gSupportHTML5Canvas == false )
    {
        if ( gSVG_VML_Support == 0 )
            return;
        if ( isFirefox ) // Use SVG (check for Firefox iframe bug)
        {
            if ( frames[gIFRAMEid].document == null ) // The iframe isn't accessible!
                return;
        }
    }

    switch ( event )
    {
        case "C1":
            if ( mid[39] < 1 )
                return;
            labelHtml = "C<sub>1</sub>";
            sky_color = "blue";
            if ( gSupportHTML5Canvas )
                moon_color = "slategrey";
            else
            {
                if ( ! isIE ) // Use SVG
                    moon_color = "slategrey";
                else // Use VML
                    moon_color = "#708090";
            }
            scx = c1_azi[0];
            scy = c1_alt[0];
            srd = c1_rad[0];
            mcx = c1_azi[1];
            mcy = c1_alt[1];
            mrd = c1_rad[1];
            angle_ns = PV[0] - c1[51];
            break;
        case "C2":
            if ( mid[39] < 2 )
                return;
            labelHtml = "C<sub>2</sub>";
            if ( gSupportHTML5Canvas )
            {
                sky_color = "midnightblue";
                moon_color = "dimgrey";
            }
            else
            {
                if ( ! isIE ) // Use SVG
                {
                    sky_color = "midnightblue";
                    moon_color = "slategrey";
                }
                else // Use VML
                {
                    sky_color = "#191970";
                    moon_color = "#708090";
                }
            }
            scx = c2_azi[0];
            scy = c2_alt[0];
            srd = c2_rad[0];
            mcx = c2_azi[1];
            mcy = c2_alt[1];
            mrd = c2_rad[1];
            if ( mid[39] > 2 ) // Total solar eclipse
                angle = ( ( gSupportHTML5Canvas ) ? (V[0] - 90.0) : V[0] ) * D2R;
            angle_ns = PV[1] - c2[51];
            break;
        case "mid":
        default:
            labelHtml = "Max";
            if ( gSupportHTML5Canvas )
            {
                if ( mid[39] > 1 ) // Total or annular solar eclipse
                {
                    sky_color = "midnightblue";
                    moon_color = "black";
                }
                else
                {
                    sky_color = "blue";
                    moon_color = "slategrey";
                }
            }
            else
            {
                if ( ! isIE ) // Use SVG
                {
                    if ( mid[39] > 1 ) // Total or annular solar eclipse
                    {
                        sky_color = "midnightblue";
                        moon_color = "black";
                    }
                    else
                    {
                        sky_color = "blue";
                        moon_color = "slategrey";
                    }
                }
                else // Use VML
                {
                    if ( mid[39] > 1 ) // Total or annular solar eclipse
                    {
                        sky_color = "#191970";
                        moon_color = "black";
                    }
                    else
                    {
                        sky_color = "blue";
                        moon_color = "#708090";
                    }
                }
            }
            scx = mid_azi[0];
            scy = mid_alt[0];
            srd = mid_rad[0];
            mcx = mid_azi[1];
            mcy = mid_alt[1];
            mrd = mid_rad[1];
            angle_ns = PV[2] - mid[51];
            break;
        case "C3":
            if ( mid[39] < 2 )
                return;
            labelHtml = "C<sub>3</sub>";
            if ( gSupportHTML5Canvas )
            {
                sky_color = "midnightblue";
                moon_color = "dimgrey";
            }
            else
            {
                if ( ! isIE ) // Use SVG
                {
                    sky_color = "midnightblue";
                    moon_color = "slategrey";
                }
                else // Use VML
                {
                    sky_color = "#191970";
                    moon_color = "#708090";
                }
            }
            scx = c3_azi[0];
            scy = c3_alt[0];
            srd = c3_rad[0];
            mcx = c3_azi[1];
            mcy = c3_alt[1];
            mrd = c3_rad[1];
            if ( mid[39] > 2 ) // Total solar eclipse
                angle = ( ( gSupportHTML5Canvas ) ? (V[1] - 90.0) : V[1] ) * D2R;
            angle_ns = PV[3] - c3[51];
            break;
        case "C4":
            if ( mid[39] < 1 )
                return;
            labelHtml = "C<sub>4</sub>";
            sky_color = "blue";
            if ( gSupportHTML5Canvas )
                moon_color = "slategrey";
            else
            {
                if ( ! isIE ) // Use SVG
                    moon_color = "slategrey";
                else // Use VML
                    moon_color = "#708090";
            }
            scx = c4_azi[0];
            scy = c4_alt[0];
            srd = c4_rad[0];
            mcx = c4_azi[1];
            mcy = c4_alt[1];
            mrd = c4_rad[1];
            angle_ns = PV[4] - c4[51];
            break;
        case "Sunrise":
            if ( mid[39] < 1 )
                return;
            if ( language == "fr" )
                labelHtml = "Lever";
            else
                labelHtml = "Rise";
            if ( gSupportHTML5Canvas )
            {
                if ( mid[39] > 1 ) // Total or annular solar eclipse
                {
                    sky_color = "midnightblue";
                    moon_color = "black";
                }
                else
                {
                    sky_color = "blue";
                    moon_color = "slategrey";
                }
            }
            else
            {
                if ( ! isIE ) // Use SVG
                {
                    if ( mid[39] > 1 ) // Total or annular solar eclipse
                    {
                        sky_color = "midnightblue";
                        moon_color = "black";
                    }
                    else
                    {
                        sky_color = "blue";
                        moon_color = "slategrey";
                    }
                }
                else // Use VML
                {
                    if ( mid[39] > 1 ) // Total or annular solar eclipse
                    {
                        sky_color = "#191970";
                        moon_color = "black";
                    }
                    else
                    {
                        sky_color = "blue";
                        moon_color = "#708090";
                    }
                }
            }
            scx = sunrise_azi[0];
            scy = sunrise_alt[0];
            srd = sunrise_rad[0];
            mcx = sunrise_azi[1];
            mcy = sunrise_alt[1];
            mrd = sunrise_rad[1];
            angle_ns = PV[0] - sunrise[51];
            break;
        case "Sunset":
            if ( mid[39] < 1 )
                return;
            if ( language == "fr" )
                labelHtml = "Coucher";
            else
                labelHtml = "Set";
            if ( gSupportHTML5Canvas )
            {
                if ( mid[39] > 1 ) // Total or annular solar eclipse
                {
                    sky_color = "midnightblue";
                    moon_color = "black";
                }
                else
                {
                    sky_color = "blue";
                    moon_color = "slategrey";
                }
            }
            else
            {
                if ( ! isIE ) // Use SVG
                {
                    if ( mid[39] > 1 ) // Total or annular solar eclipse
                    {
                        sky_color = "midnightblue";
                        moon_color = "black";
                    }
                    else
                    {
                        sky_color = "blue";
                        moon_color = "slategrey";
                    }
                }
                else // Use VML
                {
                    if ( mid[39] > 1 ) // Total or annular solar eclipse
                    {
                        sky_color = "#191970";
                        moon_color = "black";
                    }
                    else
                    {
                        sky_color = "blue";
                        moon_color = "#708090";
                    }
                }
            }
            scx = sunset_azi[0];
            scy = sunset_alt[0];
            srd = sunset_rad[0];
            mcx = sunset_azi[1];
            mcy = sunset_alt[1];
            mrd = sunset_rad[1];
            angle_ns = PV[0] - sunset[51];
            break;
    }

    if ( gSupportHTML5Canvas )
    {
        // Equirectangular projection
        var coeff = Math.cos(Math.abs((scy + mcy) / 2.0) * D2R) * 100.0 / 4.0;
        scx *= coeff;
        scy *= -100.0 / 4.0;
        srd /= 4.0;
        mcx *= coeff;
        mcy *= -100.0 / 4.0;
        mrd /= 4.0;

        var canvas = document.getElementById("SE_diagram");
        if ( canvas )
        {
            var canvas_width = parseInt(canvas.style.width, 10);
            var canvas_height = parseInt(canvas.style.height, 10);
            var canvas_width2 = parseInt((canvas_width / 2).toFixed(0), 10);
            var canvas_height2 = parseInt((canvas_height / 2).toFixed(0), 10);

            var ctx = canvas.getContext("2d");
            if ( ctx )
            {
                ctx.save();
                ctx.translate(canvas_width2, canvas_height2);

                ctx.lineWidth = 1.0;
                ctx.lineCap = "round";
                ctx.fillStyle = "#DDAD08";
                ctx.fillRect(-canvas_width2, -canvas_height2, canvas_width, canvas_height);	// Otherwise transparency problems
//        ctx.clearRect(-canvas_width2, -canvas_height2, canvas_width, canvas_height);

                // Draw the sky
                if ( scy <= canvas_height2 )
                {
                    ctx.fillStyle = sky_color;	// "#191970"
                    ctx.fillRect(-canvas_width2, -canvas_height2, canvas_width, ((scy <= -canvas_height) ? canvas_height : (((canvas_height2 - scy) >= 0) ? canvas_height2 - scy : 0)));
                }
                // Draw the Sun's corona
                if ( ( event == "mid" ) && ( mid[39] > 2 ) ) // Total solar eclipse
                {
                    var radialGradientCorona = ctx.createRadialGradient(0, 0, 0, 0, 0, srd);
                    if ( radialGradientCorona )
                    {
                        radialGradientCorona.addColorStop(0.0, "rgba(255, 255, 255, 0.8)");
                        radialGradientCorona.addColorStop(0.3, "rgba(255, 255, 255, 0.6)");
                        radialGradientCorona.addColorStop(1.0, "rgba(25, 25, 112, 0.2)");
                        ctx.save();
                        ctx.scale(2.0, 2.0);
                        ctx.fillStyle = radialGradientCorona;
                        ctx.beginPath();
                        ctx.arc(0, 0, srd, 0, 360 * D2R, false);
                        ctx.closePath();
                        ctx.fill();
                        ctx.restore();
                        ctx.save();
                        ctx.rotate((angle_ns - 8.0) * D2R);
                        ctx.translate(srd / 4, 0);
                        ctx.scale(3.5, 0.6);
                        ctx.fillStyle = radialGradientCorona;
                        ctx.beginPath();
                        ctx.arc(0, 0, srd, 0, 360 * D2R, false);
                        ctx.closePath();
                        ctx.fill();
                        ctx.restore();
                        ctx.save();
                        ctx.rotate((angle_ns + 15.0) * D2R);
                        ctx.translate(-srd / 4, 0);
                        ctx.scale(3.0, 0.5);
                        ctx.fillStyle = radialGradientCorona;
                        ctx.beginPath();
                        ctx.arc(0, 0, srd, 0, 360 * D2R, false);
                        ctx.closePath();
                        ctx.fill();
                        ctx.restore();
                    }
                }
                // Draw the Sun's N/S axis
                ctx.save();
                ctx.rotate((angle_ns + 90.0) * D2R);
                ctx.strokeStyle = "red";
                ctx.beginPath();
                ctx.moveTo(-(srd * 1.3).toFixed(0), 0);
                ctx.lineTo((srd * 1.1).toFixed(0), 0);
                ctx.closePath();
                ctx.stroke();
                ctx.restore();
                // Draw the Sun
                ctx.strokeStyle = "violet";
                ctx.fillStyle = "yellow";
                ctx.beginPath();
                ctx.arc(0, 0, srd, 0, 360 * D2R, false);
                ctx.closePath();
                ctx.fill();
                if ( ( ( event == "C2" ) || ( event == "C3" ) ) && ( mid[39] > 2 ) ) // Total solar eclipse
                    ctx.stroke();
                // Draw the Moon
                ctx.fillStyle = moon_color;
                ctx.beginPath();
                ctx.arc(mcx - scx, mcy - scy, mrd, 0, 360 * D2R, false);
                ctx.closePath();
                ctx.fill();
                // Draw the first diamond ring
                if ( ( event == "C2" ) && ( mid[39] > 2 ) ) // Total solar eclipse
                {
                    var radialGradientDiamond_1 = ctx.createRadialGradient(0, 0, 0, 0, 0, srd);
                    if ( radialGradientDiamond_1 )
                    {
                        radialGradientDiamond_1.addColorStop(0.0, "rgba(238, 130, 238, 0.8)");
                        radialGradientDiamond_1.addColorStop(0.3, "rgba(255, 255, 255, 0.6)");
                        radialGradientDiamond_1.addColorStop(1.0, "rgba(211, 211, 211, 0.5)");
                        ctx.save();
                        ctx.rotate(angle);
                        ctx.translate(srd, 0);
                        ctx.scale(0.3, 1.0);
                        ctx.strokeStyle = "rgba(211, 211, 211, 0.8)";	// "#D3D3D3"
                        ctx.fillStyle = radialGradientDiamond_1;	// "rgba(238, 130, 238, 0.8)";	// "#EE82EE"
                        ctx.beginPath();
                        ctx.arc(0, 0, srd / 2, 0, 360 * D2R, false);
                        ctx.closePath();
                        ctx.fill();
                        ctx.stroke();
                        ctx.restore();
                    }
                }
                // Draw the second diamond ring
                if ( ( event == "C3" ) && ( mid[39] > 2 ) ) // Total solar eclipse
                {
                    var radialGradientDiamond_2 = ctx.createRadialGradient(0, 0, 0, 0, 0, srd);
                    if ( radialGradientDiamond_2 )
                    {
                        radialGradientDiamond_2.addColorStop(0.0, "rgba(238, 130, 238, 0.8)");
                        radialGradientDiamond_2.addColorStop(0.3, "rgba(255, 255, 255, 0.6)");
                        radialGradientDiamond_2.addColorStop(1.0, "rgba(211, 211, 211, 0.5)");
                        ctx.save();
                        ctx.rotate(angle);
                        ctx.translate(srd, 0);
                        ctx.scale(0.3, 1.0);
                        ctx.strokeStyle = "rgba(211, 211, 211, 0.8)";	// "#D3D3D3"
                        ctx.fillStyle = radialGradientDiamond_2;	// "rgba(238, 130, 238, 0.8)";	// "#EE82EE"
                        ctx.beginPath();
                        ctx.arc(0, 0, srd / 2, 0, 360 * D2R, false);
                        ctx.closePath();
                        ctx.fill();
                        ctx.stroke();
                        ctx.restore();
                    }
                }
                // Draw the ground
                if ( scy >= -canvas_height2 )
                {
                    if ( scy > 0 ) // Sun under the horizon
                        ctx.fillStyle = "rgba(0, 100, 0, 0.8)";	// "darkgreen"
                    else
                        ctx.fillStyle = "darkgreen";	// "#006400"
                    ctx.fillRect(-canvas_width2, ((scy <= canvas_height2) ? -scy : -canvas_height2), canvas_width, canvas_height);
                }

                ctx.restore();

                // Display the event label
                document.getElementById("circum_label").innerHTML = labelHtml;
            }
        }
    }
    else
    {
        if ( ! isIE ) // Use SVG
        {
            // Equirectangular projection
            var coeff = Math.cos(Math.abs((scy + mcy) / 2.0) * D2R) * 100.0;
            scx *= coeff;
            scx = scx.toFixed(0);
            scy *= -100.0;
            scy = scy.toFixed(0);
            srd *= 1.0;
            srd = srd.toFixed(1);
            mcx *= coeff;
            mcx = mcx.toFixed(0);
            mcy *= -100.0;
            mcy = mcy.toFixed(0);
            mrd *= 1.0;
            mrd = mrd.toFixed(1);

            if ( !document.getElementById("svgdiagram") )
                return;
            var svg_dg = frames[gIFRAMEid].document.getElementById("svgdiagram");
            var svgNS = "http://www.w3.org/2000/svg";
            // Draw the sky
            var old_element = frames[gIFRAMEid].document.getElementById("sky");
            var sky = frames[gIFRAMEid].document.createElementNS(svgNS, "rect");
            sky.setAttributeNS(null, "id", "sky");
            sky.setAttributeNS(null, "x", -2000); // To take into account the azimuth 0
            sky.setAttributeNS(null, "y", -9200); // To take into account the zenith 90
            sky.setAttributeNS(null, "width", ((360 * coeff) + 4000).toFixed(0)); // To take into account the azimuth 360
            sky.setAttributeNS(null, "height", 18400); // To take into account the zenith -90
            sky.setAttributeNS(null, "fill", sky_color);
            sky.setAttributeNS(null, "stroke", sky_color);
            sky.setAttributeNS(null, "stroke-width", 0);
            if ( old_element )
                old_element.parentNode.replaceChild(sky, old_element);
            else
                svg_dg.appendChild(sky);
            bringToFront("sky");
            // Draw the Sun's corona
            var yor = parseInt(scy, 10) - 20;
            old_element = frames[gIFRAMEid].document.getElementById("coronaX1");
            if ( ( event == "mid" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                var corona = frames[gIFRAMEid].document.createElementNS(svgNS, "ellipse");
                corona.setAttributeNS(null, "id", "coronaX1");
                corona.setAttributeNS(null, "cx", scx);
                corona.setAttributeNS(null, "cy", scy);
                corona.setAttributeNS(null, "rx", srd * 7);
                corona.setAttributeNS(null, "ry", srd);
                corona.setAttributeNS(null, "fill", "url(#coronawhite)");
                corona.setAttributeNS(null, "transform", "rotate(" + (angle_ns - 8).toFixed(1) + " " + scx + " " + scy + ")");
                if ( old_element )
                    old_element.parentNode.replaceChild(corona, old_element);
                else
                    svg_dg.appendChild(corona);
                if ( yor <= 100 )
                    bringToFront("coronaX1");
            }
            else
            {
                if ( old_element )
                    old_element.setAttributeNS(null, "visibility", "hidden");
            }
            old_element = frames[gIFRAMEid].document.getElementById("coronaX2");
            if ( ( event == "mid" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                var corona = frames[gIFRAMEid].document.createElementNS(svgNS, "ellipse");
                corona.setAttributeNS(null, "id", "coronaX2");
                corona.setAttributeNS(null, "cx", scx);
                corona.setAttributeNS(null, "cy", scy);
                corona.setAttributeNS(null, "rx", srd * 6);
                corona.setAttributeNS(null, "ry", srd);
                corona.setAttributeNS(null, "fill", "url(#coronawhite)");
                corona.setAttributeNS(null, "transform", "rotate(" + (angle_ns + 15).toFixed(1) + " " + scx + " " + scy + ")");
                if ( old_element )
                    old_element.parentNode.replaceChild(corona, old_element);
                else
                    svg_dg.appendChild(corona);
                if ( yor <= 100 )
                    bringToFront("coronaX2");
            }
            else
            {
                if ( old_element )
                    old_element.setAttributeNS(null, "visibility", "hidden");
            }
            old_element = frames[gIFRAMEid].document.getElementById("corona");
            if ( ( event == "mid" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                var corona = frames[gIFRAMEid].document.createElementNS(svgNS, "circle");
                corona.setAttributeNS(null, "id", "corona");
                corona.setAttributeNS(null, "cx", scx);
                corona.setAttributeNS(null, "cy", scy);
                corona.setAttributeNS(null, "r", srd * 4);
                corona.setAttributeNS(null, "fill", "url(#coronawhite)");
                if ( old_element )
                    old_element.parentNode.replaceChild(corona, old_element);
                else
                    svg_dg.appendChild(corona);
                if ( yor <= 100 )
                    bringToFront("corona");
            }
            else
            {
                if ( old_element )
                    old_element.setAttributeNS(null, "visibility", "hidden");
            }
            // Draw the Sun's N/S axis
            old_element = frames[gIFRAMEid].document.getElementById("sun_ns");
            var sun_ns = frames[gIFRAMEid].document.createElementNS(svgNS, "line");
            sun_ns.setAttributeNS(null, "id", "sun_ns");
            var cy = (parseInt(scy, 10) - (parseFloat(srd) * 1.3)).toFixed(0);
            sun_ns.setAttributeNS(null, "x1", scx);
            sun_ns.setAttributeNS(null, "y1", cy);
            cy = (parseInt(scy, 10) + (parseFloat(srd) * 1.1)).toFixed(0);
            sun_ns.setAttributeNS(null, "x2", scx);
            sun_ns.setAttributeNS(null, "y2", cy);
            sun_ns.setAttributeNS(null, "stroke", "red");
            sun_ns.setAttributeNS(null, "stroke-width", 4);
            sun_ns.setAttributeNS(null, "transform", "rotate(" + angle_ns.toFixed(1) + " " + scx + " " + scy + ")");
            if ( old_element )
                old_element.parentNode.replaceChild(sun_ns, old_element);
            else
                svg_dg.appendChild(sun_ns);
            bringToFront("sun_ns");
            // Draw the Sun
            old_element = frames[gIFRAMEid].document.getElementById("sun");
            var sun = frames[gIFRAMEid].document.createElementNS(svgNS, "circle");
            sun.setAttributeNS(null, "id", "sun");
            sun.setAttributeNS(null, "cx", scx);
            sun.setAttributeNS(null, "cy", scy);
            sun.setAttributeNS(null, "r", srd);
            sun.setAttributeNS(null, "fill", "yellow");
            sun.setAttributeNS(null, "stroke", "red");
            if ( ( event == "C2" ) || ( event == "C3" ) )
                sun.setAttributeNS(null, "stroke-width", 1);
            else
                sun.setAttributeNS(null, "stroke-width", 0);
            if ( old_element )
                old_element.parentNode.replaceChild(sun, old_element);
            else
                svg_dg.appendChild(sun);
            bringToFront("sun");
            // Draw the Moon
            old_element = frames[gIFRAMEid].document.getElementById("moon");
            var moon = frames[gIFRAMEid].document.createElementNS(svgNS, "circle");
            moon.setAttributeNS(null, "id", "moon");
            moon.setAttributeNS(null, "cx", mcx);
            moon.setAttributeNS(null, "cy", mcy);
            moon.setAttributeNS(null, "r", mrd);
            moon.setAttributeNS(null, "fill", moon_color);
            moon.setAttributeNS(null, "stroke", "black");
            moon.setAttributeNS(null, "stroke-width", 0);
            if ( old_element )
                old_element.parentNode.replaceChild(moon, old_element);
            else
                svg_dg.appendChild(moon);
            bringToFront("moon");
            // Draw the first diamond ring
            if ( ( event == "C2" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                old_element = frames[gIFRAMEid].document.getElementById("diamond_1");
                var diamond_c2 = frames[gIFRAMEid].document.createElementNS(svgNS, "ellipse");
                diamond_c2.setAttributeNS(null, "id", "diamond_1");
                var cx = (parseInt(scx, 10) + (parseFloat(srd) * Math.sin(angle))).toFixed(0);
                var cy = (parseInt(scy, 10) - (parseFloat(srd) * Math.cos(angle))).toFixed(0);
                diamond_c2.setAttributeNS(null, "cx", cx);
                diamond_c2.setAttributeNS(null, "cy", cy);
                diamond_c2.setAttributeNS(null, "rx", srd / 2);
                diamond_c2.setAttributeNS(null, "ry", srd / 6);
                diamond_c2.setAttributeNS(null, "fill", "url(#diamondpink)");
                diamond_c2.setAttributeNS(null, "transform", "rotate(" + (angle * R2D).toFixed(1) + " " + cx + " " + cy + ")");
                if ( old_element )
                    old_element.parentNode.replaceChild(diamond_c2, old_element);
                else
                    svg_dg.appendChild(diamond_c2);
                bringToFront("diamond_1");
            }
            // Draw the second diamond ring
            if ( ( event == "C3" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                old_element = frames[gIFRAMEid].document.getElementById("diamond_2");
                var diamond_c3 = frames[gIFRAMEid].document.createElementNS(svgNS, "ellipse");
                diamond_c3.setAttributeNS(null, "id", "diamond_2");
                var cx = (parseInt(scx, 10) + (parseFloat(srd) * Math.sin(angle))).toFixed(0);
                var cy = (parseInt(scy, 10) - (parseFloat(srd) * Math.cos(angle))).toFixed(0);
                diamond_c3.setAttributeNS(null, "cx", cx);
                diamond_c3.setAttributeNS(null, "cy", cy);
                diamond_c3.setAttributeNS(null, "rx", srd / 2);
                diamond_c3.setAttributeNS(null, "ry", srd / 6);
                diamond_c3.setAttributeNS(null, "fill", "url(#diamondpink)");
                diamond_c3.setAttributeNS(null, "transform", "rotate(" + (angle * R2D).toFixed(1) + " " + cx + " " + cy + ")");
                if ( old_element )
                    old_element.parentNode.replaceChild(diamond_c3, old_element);
                else
                    svg_dg.appendChild(diamond_c3);
                bringToFront("diamond_2");
            }
            // Draw the ground
            old_element = frames[gIFRAMEid].document.getElementById("ground");
            var ground = frames[gIFRAMEid].document.createElementNS(svgNS, "rect");
            ground.setAttributeNS(null, "id", "ground");
            ground.setAttributeNS(null, "x", -400); // To take into account the azimuth 0
            ground.setAttributeNS(null, "y", 0);
            ground.setAttributeNS(null, "width", ((360 * coeff) + 400).toFixed(0)); // To take into account the azimuth 360
            ground.setAttributeNS(null, "height", 9000);
            ground.setAttributeNS(null, "fill", "darkgreen");
            if ( scy > 0 ) // Sun under the horizon
                ground.setAttributeNS(null, "fill-opacity", 0.8);
            else
                ground.setAttributeNS(null, "fill-opacity", 1.0);
            ground.setAttributeNS(null, "stroke", "darkgreen");
            ground.setAttributeNS(null, "stroke-width", 0);
            if ( old_element )
                old_element.parentNode.replaceChild(ground, old_element);
            else
                svg_dg.appendChild(ground);
            bringToFront("ground");
            // Adjust the viewport
            var svg = frames[gIFRAMEid].document.getElementById("svgdisplay");
            if ( svg )
                svg.setAttributeNS(null, "viewBox", (scx - 90) + " " + (scy - 90) + " 180 180");
            // Display the event label
            if ( isFirefox )
                document.getElementById("circum_label").innerHTML = labelHtml;
            else
                frames[gIFRAMEid].document.getElementById("circum_label").innerHTML = labelHtml;
        }
        else // Use VML
        {
            // Equirectangular projection
            var coeff = Math.cos(Math.abs((scy + mcy) / 2) * D2R) * 25.0;
            scx *= coeff;
            scx = scx.toFixed(0);
            scy *= -25.0;
            scy = scy.toFixed(0);
            srd /= 4.0;
            srd = srd.toFixed(1);
            mcx *= coeff;
            mcx = mcx.toFixed(0);
            mcy *= -25.0;
            mcy = mcy.toFixed(0);
            mrd /= 4.0;
            mrd = mrd.toFixed(1);

            var xor = parseInt(scx, 10) - 50;
            var yor = parseInt(scy, 10) - 20;
            var html = '<v:group id="xse_diagram" style="width: 100px; height: 40px; clip: rect(' + yor + 'px ' + (xor + 100) + 'px ' + (yor + 40) + 'px ' + xor + 'px); overflow: hidden;" coordsize="100,40" coordorigin="' + xor + ',' + yor + '">';
            // Draw the sky
            if ( yor < 0 )
                html += ' <v:rect id="sky" style="left: ' + xor + 'px; top: ' + yor + 'px; width: 100px; height: ' + ((yor < -40) ? '40' : -yor) + 'px;" fillcolor="' + sky_color + '" stroked="false" strokecolor="' + sky_color + '" strokeweight="0"></v:rect>';
            // Draw the Sun's corona
            if ( ( event == "mid" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                if ( yor <= 5 )
                {
                    /*          html += ' <v:oval id="coronaX1" style="left: ' + (parseInt(scx, 10) - (parseFloat(srd) * 4)).toFixed(0) + 'px; top: ' + (parseInt(scy, 10) - (parseFloat(srd) * 0.75)).toFixed(0) + 'px; width: ' + (parseFloat(srd) * 8).toFixed(0) + 'px; height: ' + (parseFloat(srd) * 1.5).toFixed(0) + 'px; rotation: ' + (angle_ns - 8).toFixed(1) + ';" fillcolor="white" stroked="false" strokecolor="' + sky_color + '" strokeweight="0">';
                     html += '  <v:fill id="corona_gradient1" type="gradientradial" color2="' + sky_color + '" colors="0% white, 30% white, 100% ' + sky_color + '" focusposition="0.5,0.5" focussize="0.0,0.0" focus="100%" opacity="20%" />';
                     html += ' </v:oval>';
                     html += ' <v:oval id="coronaX2" style="left: ' + (parseInt(scx, 10) - (parseFloat(srd) * 3)).toFixed(0) + 'px; top: ' + (parseInt(scy, 10) - (parseFloat(srd) * 0.75)).toFixed(0) + 'px; width: ' + (parseFloat(srd) * 6).toFixed(0) + 'px; height: ' + Math.floor(parseFloat(srd) * 1.5) + 'px; rotation: ' + (angle_ns + 15).toFixed(1) + ';" fillcolor="white" stroked="false" strokecolor="' + sky_color + '" strokeweight="0">';
                     html += '  <v:fill id="corona_gradient2" type="gradientradial" color2="' + sky_color + '" colors="0% white, 30% white, 100% ' + sky_color + '" focusposition="0.5,0.5" focussize="0.0,0.0" focus="100%" opacity="20%" />';
                     html += ' </v:oval>';*/
                    html += ' <v:oval id="corona" style="left: ' + (parseInt(scx, 10) - (parseFloat(srd) * 2)).toFixed(0) + 'px; top: ' + (parseInt(scy, 10) - (parseFloat(srd) * 2)).toFixed(0) + 'px; width: ' + (parseFloat(srd) * 4).toFixed(0) + 'px; height: ' + (parseFloat(srd) * 4).toFixed(0) + 'px;" fillcolor="white" stroked="false" strokecolor="' + sky_color + '" strokeweight="0">';
                    html += '  <v:fill id="corona_gradient" type="gradientradial" color2="' + sky_color + '" colors="0% white, 30% white, 100% ' + sky_color + '" focusposition="0.5,0.5" focussize="0.0,0.0" focus="100%" opacity="60%" />';
                    html += ' </v:oval>';
                }
            }
            // Draw the Sun's N/S axis
            var x1 = parseInt(scx, 10);
            var y1 = (parseInt(scy, 10) - (parseFloat(srd) * 1.3)).toFixed(0);
            var x2 = x1;
            var y2 = (parseInt(scy, 10) + (parseFloat(srd) * 1.1)).toFixed(0);
            html += ' <v:line id="sun_ns" from="' + x1 + ',' + y1 + '" to="' + x2 + ',' + y2 + '" coordorigin="-500 -500" coordsize="1000 1000" style="rotation: ' + angle_ns.toFixed(1) + ';" strokecolor="red" strokeweight="1" />';
            // Draw the Sun
            if ( ( event == "C2" ) || ( event == "C3" ) )
                html += ' <v:oval id="sun" style="left: ' + (parseInt(scx, 10) - parseFloat(srd)).toFixed(0) + 'px; top: ' + (parseInt(scy, 10) - parseFloat(srd)).toFixed(0) + 'px; width: ' + (parseFloat(srd) * 2).toFixed(0) + 'px; height: ' + (parseFloat(srd) * 2).toFixed(0) + 'px;" fillcolor="yellow" stroked="false" strokecolor="red" strokeweight="0"></v:oval>';
            else
                html += ' <v:oval id="sun" style="left: ' + (parseInt(scx, 10) - parseFloat(srd)).toFixed(0) + 'px; top: ' + (parseInt(scy, 10) - parseFloat(srd)).toFixed(0) + 'px; width: ' + (parseFloat(srd) * 2).toFixed(0) + 'px; height: ' + (parseFloat(srd) * 2).toFixed(0) + 'px;" fillcolor="yellow" stroked="false" strokecolor="yellow" strokeweight="0"></v:oval>';
            // Draw the Moon
            html += ' <v:oval id="moon" style="left: ' + (parseInt(mcx, 10) - parseFloat(mrd)).toFixed(0) + 'px; top: ' + (parseInt(mcy, 10) - parseFloat(mrd)).toFixed(0) + 'px; width: ' + (parseFloat(mrd) * 2).toFixed(0) + 'px; height: ' + (parseFloat(mrd) * 2).toFixed(0) + 'px;" fillcolor="' + moon_color + '" stroked="false" strokecolor="' + moon_color + '" strokeweight="0"></v:oval>';
            // Draw the first diamond ring
            if ( ( event == "C2" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                var cx = (parseInt(scx, 10) - (parseFloat(srd) / 2) + (parseFloat(srd) * Math.sin(angle))).toFixed(0);
                var cy = (parseInt(scy, 10) - (parseFloat(srd) / 6) - (parseFloat(srd) * Math.cos(angle))).toFixed(0);
                html += ' <v:oval id="diamond_1" style="left: ' + cx + '; top: ' + cy + '; width: ' + parseFloat(srd).toFixed(0) + '; height: ' + (parseFloat(srd) / 3).toFixed(0) + '; rotation: ' + (angle * R2D).toFixed(1) + ';" fillcolor="#EE82EE" stroked="false" strokecolor="#D3D3D3" strokeweight="0">';
                html += '  <v:fill id="diamond_1_gradient" type="gradientradial" color2="#D3D3D3" colors="0% #EE82EE, 30% white, 90% #D3D3D3" focusposition="0.5,0.5" focussize="0.0,0.0" focus="100%" opacity="60%" />';
                html += ' </v:oval>';
            }
            // Draw the second diamond ring
            if ( ( event == "C3" ) && ( mid[39] > 2 ) ) // Total solar eclipse
            {
                var cx = (parseInt(scx, 10) - (parseFloat(srd) / 2) + (parseFloat(srd) * Math.sin(angle))).toFixed(0);
                var cy = (parseInt(scy, 10) - (parseFloat(srd) / 6) - (parseFloat(srd) * Math.cos(angle))).toFixed(0);
                html += ' <v:oval id="diamond_2" style="left: ' + cx + 'px; top: ' + cy + 'px; width: ' + parseFloat(srd).toFixed(0) + 'px; height: ' + (parseFloat(srd) / 3).toFixed(0) + 'px; rotation: ' + (angle * R2D).toFixed(1) + ';" fillcolor="#EE82EE" stroked="false" strokecolor="#D3D3D3" strokeweight="0">';
                html += '  <v:fill id="diamond_2_gradient" type="gradientradial" color2="#D3D3D3" colors="0% #EE82EE, 30% white, 90% #D3D3D3" focusposition="0.5,0.5" focussize="0.0,0.0" focus="100%" opacity="60%" />';
                html += ' </v:oval>';
            }
            // Draw the ground
            if ( yor > -40 )
            {
                html += ' <v:rect id="ground" style="left: ' + xor + 'px; top: ' + ((yor < 0) ? '0' : yor) + 'px; width: 100px; height: ' + ((yor >= 40) ? '40' : ((yor >= 0) ? '40' : (40 + yor))) + 'px;" fillcolor="#006400" stroked="false" strokecolor="#006400" strokeweight="0">';
                if ( scy > 0 )
                    html += '  <v:fill id="ground_opacity" type="solid" color="green" opacity="70%" />';
                html += ' </v:rect>';
            }
            html += '</v:group>';
            html += '<v:rect id="sky2" style="left: 0px; top: 0px; width: 100px; height: 40px;" fillcolor="' + sky_color + '" stroked="false" strokecolor="' + sky_color + '" strokeweight="0"></v:rect>';	// To trigger the diagram with the VML buggy IE 8!!!!
            // Display the event label
            html += '<div id="VML_label" align="left" style="position: relative; left: 5px; top: 2px; display: block; text-align: left; color: #FF6600; height: 0px;">' + labelHtml + '</div>';

            if ( inline == false )
            {
                if ( document.getElementById("eclipse_diagram") != null )
                    document.getElementById("eclipse_diagram").innerHTML = html;
                else
                    return html;
            }
            else
                return html;
        }
    }

    if ( typeof daynight !== "undefined" )
    {
        var numDateDN = new Object();
        switch ( event )
        {
            case "C1":
                getUTdatetime(c1, numDateDN);
                break;
            case "C2":
                getUTdatetime(c2, numDateDN);
                break;
            case "mid":
            default:
                getUTdatetime(mid, numDateDN);
                break;
            case "C3":
                getUTdatetime(c3, numDateDN);
                break;
            case "C4":
                getUTdatetime(c4, numDateDN);
                break;
            case "Sunrise":
                getUTdatetime(sunrise, numDateDN);
                break;
            case "Sunset":
                getUTdatetime(sunset, numDateDN);
                break;
        }
        daynight.setDate(new Date(numDateDN.year, numDateDN.month - 1, numDateDN.day, numDateDN.hour, numDateDN.minute, numDateDN.second, numDateDN.millisecond));	// At maximum eclipse by default
    }
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

//
// AJAX functions
function loadXMLCalc( url, isAsync )
{
    // To allow the cross-domain XMLHttpRequest in Firefox
    if ( location )
    {
        if ( location.protocol == "file:" )
        {
            if ( ( typeof netscape !== "undefined" ) && ( typeof netscape.security !== "undefined" ) )
            {
                try {
                    netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
                }
                catch (e) {
                    if ( myLanguage == "fr" )
                    {
//            updateErrorLog("fr", "Permission UniversalBrowserRead refus\xE9e");
                        alert("Permission UniversalBrowserRead refus\xE9e.");
                    }
                    else
                    {
//            updateErrorLog("en", "Permission UniversalBrowserRead denied");
                        alert("Permission UniversalBrowserRead denied.");
                    }
                }
            }
        }
    }

    gXMLRequest = false;
    if ( window.XMLHttpRequest )      // Native XMLHttpRequest object
    {
        try {
            gXMLRequest = new XMLHttpRequest(); // Firefox, Safari, Opera, ...
        }
        catch(e) {
            gXMLRequest = false;
        }
        if ( gXMLRequest.overrideMimeType )
            gXMLRequest.overrideMimeType("text/xml");
    }
    else if ( window.ActiveXObject )   // IE/Windows ActiveX version
    {
        try {
            gXMLRequest = new ActiveXObject("Msxml2.XMLHTTP"); // IE 6+
        }
        catch(e) {
            try {
                gXMLRequest = new ActiveXObject("Microsoft.XMLHTTP"); // IE 5.5+
            }
            catch(e) {
                gXMLRequest = false;
            }
        }
    }
    else                               // XMLHttpRequest unsupported by the browser
    {
        if ( myLanguage == "fr" )
        {
//      updateErrorLog("fr", "Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
            alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
        }
        else
        {
//      updateErrorLog("en", "Your browser has no support for XMLHTTPRequest...");
            alert("Your browser has no support for XMLHTTPRequest...");
        }
    }

    if ( gXMLRequest )
    {
        gXMLRequest.onreadystatechange = retrieveLimbCorrections;
        gXMLRequest.open("GET", url, isAsync);
        gXMLRequest.send(null);
    }
    else
    {
        if ( myLanguage == "fr" )
        {
//      updateErrorLog("fr", "Impossible de cr\xE9er un conteneur XMLHTTP");
            alert("Impossible de cr\xE9er un conteneur XMLHTTP.");
        }
        else
        {
//      updateErrorLog("en", "Cannot create XMLHTTP instance");
            alert("Cannot create XMLHTTP instance.");
        }
    }
}

function retrieveLimbCorrections( )
{
    if ( gXMLRequest.readyState == 4 )         // Complete (check gXMLRequest.status and gXMLRequest.statusText)
    {
        if ( gXMLRequest.status == 200 )         // OK (can be 404, ...)
            loadLimbCorrections(gXMLRequest.responseXML);
        else
        {
            setTimeout("displayNoLimbCorrections()", 1000);
            if ( console )
                console.log("There was a problem retrieving the XML data:\n(" + gXMLRequest.status + ") " + gXMLRequest.statusText);
            /*      if ( myLanguage == "fr" )
             updateErrorLog("fr", "Un probl\xE8me est survenu lors de la lecture des donn\xE9es XML:\n(" + gXMLRequest.status + ") " + gXMLRequest.statusText);
             else
             updateErrorLog("en", "There was a problem retrieving the XML data:\n(" + gXMLRequest.status + ") " + gXMLRequest.statusText);*/
        }
    }
}

function loadLimbCorrections( xmldoc )
{
    var c2_value, c3_value;

    // To allow the cross-domain XMLHttpRequest in Firefox
    if ( location )
    {
        if ( location.protocol == "file:" )
        {
            if ( ( typeof netscape !== "undefined" ) && ( typeof netscape.security !== "undefined" ) )
            {
                try {
                    netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
                }
                catch (e) {
                    if ( myLanguage == "fr" )
                    {
//            updateErrorLog("fr", "Permission UniversalBrowserRead refus\xE9e");
                        alert("Permission UniversalBrowserRead refus\xE9e.");
                    }
                    else
                    {
//            updateErrorLog("en", "Permission UniversalBrowserRead denied");
                        alert("Permission UniversalBrowserRead denied.");
                    }
                }
            }
        }
    }

    if ( xmldoc != null )
    {
        var limbCorrections = xmldoc.getElementsByTagName("limb_corrections");
        if ( limbCorrections.length > 0 )
        {
            for ( var i = 0; i < limbCorrections.length; i++ )
            {
                c2_value = parseFloat((xmldoc.getElementsByTagName("c2_value")[i].firstChild.nodeValue).replace(/,/, '.'));
                c3_value = parseFloat((xmldoc.getElementsByTagName("c3_value")[i].firstChild.nodeValue).replace(/,/, '.'));

                c2[36] = c2_value;
                c3[36] = c3_value;
                if (hasValidLimbCorrections() == true)
                    setTimeout("displayLimbCorrections()", 1000);
                else
                    setTimeout("displayNoLimbCorrections()", 1000);
            }
        }
        else
            setTimeout("displayNoLimbCorrections()", 1000);
    }
    else
        setTimeout("displayNoLimbCorrections()", 1000);
}

function hasValidLimbCorrections( )
{
    var validLimbCorrections = false;

    if (mid[39] == 2)
    {
        if ((Math.abs(c2[36]) < 32.0) && (Math.abs(c3[36]) < 32.0))
            validLimbCorrections = true;
    }
    else
    {
        if ((Math.abs(c2[36]) < 42.0) && (Math.abs(c3[36]) < 42.0))
            validLimbCorrections = true;
    }

    return validLimbCorrections;
}

function displayLimbCorrections( )
{
    if ( document.getElementById("c2_lc") )
        document.getElementById("c2_lc").innerHTML = getlc(c2, myLanguage);
    if ( document.getElementById("c3_lc") )
        document.getElementById("c3_lc").innerHTML = getlc(c3, myLanguage);

    if ( document.getElementById("duration_lc") )
        document.getElementById("duration_lc").innerHTML = getdurationlc(myLanguage);

    if ( document.getElementById("c2_time") )
        document.getElementById("c2_time").setAttribute("title", gettimelc(c2, myLanguage));
    if ( document.getElementById("c3_time") )
        document.getElementById("c3_time").setAttribute("title", gettimelc(c3, myLanguage));
}

function displayNoLimbCorrections( )
{
    if ( myLanguage == "fr" )
    {
        if ( document.getElementById("c2_lc") )
            document.getElementById("c2_lc").innerHTML = "--,-s";
        if ( document.getElementById("c3_lc") )
            document.getElementById("c3_lc").innerHTML = "--,-s";
    }
    else
    {
        if ( document.getElementById("c2_lc") )
            document.getElementById("c2_lc").innerHTML = "--.-s";
        if ( document.getElementById("c3_lc") )
            document.getElementById("c3_lc").innerHTML = "--.-s";
    }

    if ( document.getElementById("duration_lc") )
        document.getElementById("duration_lc").innerHTML = getdurationlc(myLanguage);

    if ( document.getElementById("c2_time") )
        document.getElementById("c2_time").setAttribute("title", "");
    if ( document.getElementById("c3_time") )
        document.getElementById("c3_time").setAttribute("title", "");
}

function displayLocalLimbCorrections( )
{
    if (hasValidLimbCorrections() == true)
        displayLimbCorrections();
    else
        displayNoLimbCorrections();
}

function CheckSVG_VML( )
{
    gSVG_VML_Support = 0;
    if (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#SVG", "1.1")) // SVG 1.1
        gSVG_VML_Support = 2;
    else
    {
        if (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1")) // Shapes 1.1
            gSVG_VML_Support = 2;
        else
        {
            if (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#CoreAttribute", "1.1")) // Shapes 1.1
                gSVG_VML_Support = 2;
            else if (document.implementation.hasFeature("org.w3c.dom.svg", "1.0")) // SVG 1.0
                gSVG_VML_Support = 2;
        }
    }
    if ( gSVG_VML_Support == 0 )
    {
        if ( isIE )
            gSVG_VML_Support = 1;
    }

    return gSVG_VML_Support;
}

//
// Get the sunrise circumstances
function getsunrise( circumstances )
{
    var t, ans, alt;

    circumstances[0] = -2;
    circumstances[1] = c1[1] - 0.8;
    circumstances[32] = -1.0;
    t = circumstances[1];
    var sinlat = Math.sin(obsvconst[0]);
    var coslat = Math.cos(obsvconst[0]);

    do
    {
        t += 1.0 / 180.0;	// Every 20 seconds

        // x
        ans = elements[9] * t + elements[8];
        ans = ans * t + elements[7];
        ans = ans * t + elements[6];
        circumstances[2] = ans;
        // dx
        ans = 3.0 * elements[9] * t + 2.0 * elements[8];
        ans = ans * t + elements[7];
        circumstances[10] = ans;
        // y
        ans = elements[13] * t + elements[12];
        ans = ans * t + elements[11];
        ans = ans * t + elements[10];
        circumstances[3] = ans;
        // dy
        ans = 3.0 * elements[13] * t + 2.0 * elements[12];
        ans = ans * t + elements[11];
        circumstances[11] = ans;
        // d
        ans = elements[16] * t + elements[15];
        ans = ans * t + elements[14];
        ans *= D2R;
        circumstances[4] = ans;
        // sin d and cos d
        circumstances[5] = Math.sin(ans);
        circumstances[6] = Math.cos(ans);
        // m
        ans = elements[19] * t + elements[18];
        ans = ans * t + elements[17];
        if (ans >= 360.0)
            ans -= 360.0;
        ans *= D2R;
        circumstances[7] = ans;
        // h, sin h, cos h
        circumstances[16] = circumstances[7] - obsvconst[1] - (elements[5] / 13713.440924999626077);
        circumstances[17] = Math.sin(circumstances[16]);
        circumstances[18] = Math.cos(circumstances[16]);

        // alt
        circumstances[32] = Math.asin((circumstances[5] * sinlat) + (circumstances[6] * coslat * circumstances[18]));
    }
    while ( ( circumstances[32] < gRefractionHeight ) && ( Math.abs(t - mid[1]) < 2.0 ) );
    if ( ( circumstances[32] < 0.0 ) && ( Math.abs(t - mid[1]) < 2.0 ) )
    {
        circumstances[1] = t;
        circumstances[40] = 2;
    }
    else
    {
        circumstances[1] = mid[1];
        circumstances[40] = 4;
        return;
    }

    // dd
    ans = 2.0 * elements[16] * t + elements[15];
    ans *= D2R;
    circumstances[12] = ans;
    // dm
    ans = 2.0 * elements[19] * t + elements[18];
    ans *= D2R;
    circumstances[13] = ans;
    // xi
    circumstances[19] = obsvconst[5] * circumstances[17];
    // eta
    circumstances[20] = (obsvconst[4] * circumstances[6]) - (obsvconst[5] * circumstances[18] * circumstances[5]);
    // zeta
    circumstances[21] = (obsvconst[4] * circumstances[5]) + (obsvconst[5] * circumstances[18] * circumstances[6]);
    // dxi
    circumstances[22] = circumstances[13] * obsvconst[5] * circumstances[18];
    // deta
    circumstances[23] = (circumstances[13] * circumstances[19] * circumstances[5]) - (circumstances[21] * circumstances[12]);
    // u
    circumstances[24] = circumstances[2] - circumstances[19];
    // v
    circumstances[25] = circumstances[3] - circumstances[20];

    // q
    circumstances[33] = Math.asin(coslat * circumstances[17] / Math.cos(circumstances[32]));
    if (circumstances[20] < 0.0)
        circumstances[33] = Math.PI - circumstances[33];
    // azi
    circumstances[35] = Math.atan2(-circumstances[17] * circumstances[6], (circumstances[5] * coslat) - (circumstances[18] * sinlat * circumstances[6]));
    var type = circumstances[0];
    // l1 and dl1
    ans = elements[22] * t + elements[21];
    ans = ans * t + elements[20];
    circumstances[8] = ans;
    if ((type == -2) || (type == 0) || (type == 2))
        circumstances[14] = 2.0 * elements[22] * t + elements[21];
    // l2 and dl2
    ans = elements[25] * t + elements[24];
    ans = ans * t + elements[23];
    circumstances[9] = ans;
    if ((type == -1) || (type == 0) || (type == 1))
        circumstances[15] = 2.0 * elements[25] * t + elements[24];
    // l1'
    circumstances[28] = circumstances[8] - (circumstances[21] * elements[26]);
    // l2'
    circumstances[29] = circumstances[9] - (circumstances[21] * elements[27]);
    // m, magnitude and Moon/Sun ratio
    circumstances[36] = Math.sqrt((circumstances[24] * circumstances[24]) + (circumstances[25] * circumstances[25]));
    circumstances[37] = (circumstances[28] - circumstances[36]) / (circumstances[28] + circumstances[29]);
    circumstances[38] = (circumstances[28] - circumstances[29]) / (circumstances[28] + circumstances[29]);

    var xi = circumstances[19];
    var eta = circumstances[20];
    var zeta = circumstances[21];
    // Sun distance in unit of the earth equatorial radius
    var zs = (circumstances[8] * Math.cos(f1)) - (circumstances[9] * Math.cos(f2));
    zs /= Math.sin(f1) - Math.sin(f2);
    // Moon distance in unit of the earth equatorial radius
    var zm = (circumstances[8] * Math.cos(f1)) + (lambdak1k2 * circumstances[9] * Math.cos(f2));
    zm /= Math.sin(f1) + (lambdak1k2 * Math.sin(f2));
    var u = circumstances[2] - xi;
    var v = circumstances[3] - eta;
    zs -= zeta;
    zm -= zeta;
    var tmp = Math.sqrt((u * u) + (v * v) + (zs * zs));
    var sdec = (v * circumstances[6]) + (zs * circumstances[5]);
    sdec = Math.asin(sdec / tmp);
    tmp = Math.sqrt((u * u) + (v * v) + (zm * zm));
    var mdec = (v * circumstances[6]) + (zm * circumstances[5]);
    mdec = Math.asin(mdec / tmp);
    var deltamus = Math.atan(u / ((v * circumstances[5]) - (zs * circumstances[6])));
    var deltamum = Math.atan(u / ((v * circumstances[5]) - (zm * circumstances[6])));
    var sha = circumstances[7] + deltamus;
    var mha = circumstances[7] + deltamum;
    // Local hour angle
    sha -= obsvconst[1] + (elements[5] / 13713.440924999626077);
    mha -= obsvconst[1] + (elements[5] / 13713.440924999626077);
    var sinsdec = Math.sin(sdec);
    var cossdec = Math.cos(sdec);
    var sinsha = Math.sin(sha);
    var cossha = Math.cos(sha);
    // Sun altitude
    circumstances[45] = Math.asin((sinsdec * sinlat) + (cossdec * cossha * coslat));
    // Sun azimuth
    circumstances[46] = Math.atan2(-cossdec * sinsha, (sinsdec * coslat) - (cossdec * cossha * sinlat));
    var sinmdec = Math.sin(mdec);
    var cosmdec = Math.cos(mdec);
    var sinmha = Math.sin(mha);
    var cosmha = Math.cos(mha);
    // Moon altitude
    circumstances[41] = Math.asin((sinmdec * sinlat) + (cosmdec * cosmha * coslat));
    // Moon azimuth
    circumstances[42] = Math.atan2(-cosmdec * sinmha, (sinmdec * coslat) - (cosmdec * cosmha * sinlat));
    // Sun apparent radius
    tmp = (circumstances[8] * Math.cos(f1) * Math.sin(f2)) - (circumstances[9] * Math.sin(f1) * Math.cos(f2));
    var R = tmp / (Math.sin(f1) - Math.sin(f2));
    var rs = Math.asin(R / Math.sqrt((u * u) + (v * v) + (zs * zs))); // Topocentric
    circumstances[43] = rs * R2D;
    // Moon apparent radius
    var k = tmp / ((Math.sin(f1) / lambdak1k2) + Math.sin(f2));
    var rm = Math.asin(k / Math.sqrt((u * u) + (v * v) + (zm * zm))); // Topocentric
    circumstances[44] = rm * R2D;
    sunrise_alt[0] = circumstances[45] * R2D; // Sun
    sunrise_azi[0] = circumstances[46] * R2D;
    if (sunrise_azi[0] < 0.0)
        sunrise_azi[0] += 360.0;
    else if (sunrise_azi[0] >= 360.0)
        sunrise_azi[0] -= 360.0;
    sunrise_rad[0] = circumstances[43] * 100;
    sunrise_alt[1] = circumstances[41] * R2D; // Moon
    sunrise_azi[1] = circumstances[42] * R2D;
    if (sunrise_azi[1] < 0.0)
        sunrise_azi[1] += 360.0;
    else if (sunrise_azi[1] >= 360.0)
        sunrise_azi[1] -= 360.0;
    sunrise_rad[1] = circumstances[44] * 100;

    var jd = getjd(circumstances);
    circumstances[51] = getsn(jd);	// Sun axis from celestial north
}

//
// Get the sunset circumstances
function getsunset( circumstances )
{
    var t, ans, alt;

    circumstances[0] = 2;
    circumstances[1] = c4[1] + 0.8;
    circumstances[32] = -1.0;
    t = circumstances[1];
    var sinlat = Math.sin(obsvconst[0]);
    var coslat = Math.cos(obsvconst[0]);

    do
    {
        t -= 1.0 / 180.0;	// Every 20 seconds

        // x
        ans = elements[9] * t + elements[8];
        ans = ans * t + elements[7];
        ans = ans * t + elements[6];
        circumstances[2] = ans;
        // dx
        ans = 3.0 * elements[9] * t + 2.0 * elements[8];
        ans = ans * t + elements[7];
        circumstances[10] = ans;
        // y
        ans = elements[13] * t + elements[12];
        ans = ans * t + elements[11];
        ans = ans * t + elements[10];
        circumstances[3] = ans;
        // dy
        ans = 3.0 * elements[13] * t + 2.0 * elements[12];
        ans = ans * t + elements[11];
        circumstances[11] = ans;
        // d
        ans = elements[16] * t + elements[15];
        ans = ans * t + elements[14];
        ans *= D2R;
        circumstances[4] = ans;
        // sin d and cos d
        circumstances[5] = Math.sin(ans);
        circumstances[6] = Math.cos(ans);
        // m
        ans = elements[19] * t + elements[18];
        ans = ans * t + elements[17];
        if (ans >= 360.0)
            ans -= 360.0;
        ans *= D2R;
        circumstances[7] = ans;
        // h, sin h, cos h
        circumstances[16] = circumstances[7] - obsvconst[1] - (elements[5] / 13713.440924999626077);
        circumstances[17] = Math.sin(circumstances[16]);
        circumstances[18] = Math.cos(circumstances[16]);

        // alt
        circumstances[32] = Math.asin((circumstances[5] * sinlat) + (circumstances[6] * coslat * circumstances[18]));
    }
    while ( ( circumstances[32] < gRefractionHeight ) && ( Math.abs(t - mid[1]) < 2.0 ) );
    if ( ( circumstances[32] < 0.0 ) && ( Math.abs(t - mid[1]) < 2.0 ) )
    {
        circumstances[1] = t;
        circumstances[40] = 3;
    }
    else
    {
        circumstances[1] = mid[1];
        circumstances[40] = 4;
        return;
    }

    // dd
    ans = 2.0 * elements[16] * t + elements[15];
    ans *= D2R;
    circumstances[12] = ans;
    // dm
    ans = 2.0 * elements[19] * t + elements[18];
    ans *= D2R;
    circumstances[13] = ans;
    // xi
    circumstances[19] = obsvconst[5] * circumstances[17];
    // eta
    circumstances[20] = (obsvconst[4] * circumstances[6]) - (obsvconst[5] * circumstances[18] * circumstances[5]);
    // zeta
    circumstances[21] = (obsvconst[4] * circumstances[5]) + (obsvconst[5] * circumstances[18] * circumstances[6]);
    // dxi
    circumstances[22] = circumstances[13] * obsvconst[5] * circumstances[18];
    // deta
    circumstances[23] = (circumstances[13] * circumstances[19] * circumstances[5]) - (circumstances[21] * circumstances[12]);
    // u
    circumstances[24] = circumstances[2] - circumstances[19];
    // v
    circumstances[25] = circumstances[3] - circumstances[20];

    // q
    circumstances[33] = Math.asin(coslat * circumstances[17] / Math.cos(circumstances[32]));
    if (circumstances[20] < 0.0)
        circumstances[33] = Math.PI - circumstances[33];
    // azi
    circumstances[35] = Math.atan2(-circumstances[17] * circumstances[6], (circumstances[5] * coslat) - (circumstances[18] * sinlat * circumstances[6]));
    var type = circumstances[0];
    // l1 and dl1
    ans = elements[22] * t + elements[21];
    ans = ans * t + elements[20];
    circumstances[8] = ans;
    if ((type == -2) || (type == 0) || (type == 2))
        circumstances[14] = 2.0 * elements[22] * t + elements[21];
    // l2 and dl2
    ans = elements[25] * t + elements[24];
    ans = ans * t + elements[23];
    circumstances[9] = ans;
    if ((type == -1) || (type == 0) || (type == 1))
        circumstances[15] = 2.0 * elements[25] * t + elements[24];
    // l1'
    circumstances[28] = circumstances[8] - (circumstances[21] * elements[26]);
    // l2'
    circumstances[29] = circumstances[9] - (circumstances[21] * elements[27]);
    // m, magnitude and Moon/Sun ratio
    circumstances[36] = Math.sqrt((circumstances[24] * circumstances[24]) + (circumstances[25] * circumstances[25]));
    circumstances[37] = (circumstances[28] - circumstances[36]) / (circumstances[28] + circumstances[29]);
    circumstances[38] = (circumstances[28] - circumstances[29]) / (circumstances[28] + circumstances[29]);

    var xi = circumstances[19];
    var eta = circumstances[20];
    var zeta = circumstances[21];
    // Sun distance in unit of the earth equatorial radius
    var zs = (circumstances[8] * Math.cos(f1)) - (circumstances[9] * Math.cos(f2));
    zs /= Math.sin(f1) - Math.sin(f2);
    // Moon distance in unit of the earth equatorial radius
    var zm = (circumstances[8] * Math.cos(f1)) + (lambdak1k2 * circumstances[9] * Math.cos(f2));
    zm /= Math.sin(f1) + (lambdak1k2 * Math.sin(f2));
    var u = circumstances[2] - xi;
    var v = circumstances[3] - eta;
    zs -= zeta;
    zm -= zeta;
    var tmp = Math.sqrt((u * u) + (v * v) + (zs * zs));
    var sdec = (v * circumstances[6]) + (zs * circumstances[5]);
    sdec = Math.asin(sdec / tmp);
    tmp = Math.sqrt((u * u) + (v * v) + (zm * zm));
    var mdec = (v * circumstances[6]) + (zm * circumstances[5]);
    mdec = Math.asin(mdec / tmp);
    var deltamus = Math.atan(u / ((v * circumstances[5]) - (zs * circumstances[6])));
    var deltamum = Math.atan(u / ((v * circumstances[5]) - (zm * circumstances[6])));
    var sha = circumstances[7] + deltamus;
    var mha = circumstances[7] + deltamum;
    // Local hour angle
    sha -= obsvconst[1] + (elements[5] / 13713.440924999626077);
    mha -= obsvconst[1] + (elements[5] / 13713.440924999626077);
    var sinsdec = Math.sin(sdec);
    var cossdec = Math.cos(sdec);
    var sinsha = Math.sin(sha);
    var cossha = Math.cos(sha);
    // Sun altitude
    circumstances[45] = Math.asin((sinsdec * sinlat) + (cossdec * cossha * coslat));
    // Sun azimuth
    circumstances[46] = Math.atan2(-cossdec * sinsha, (sinsdec * coslat) - (cossdec * cossha * sinlat));
    var sinmdec = Math.sin(mdec);
    var cosmdec = Math.cos(mdec);
    var sinmha = Math.sin(mha);
    var cosmha = Math.cos(mha);
    // Moon altitude
    circumstances[41] = Math.asin((sinmdec * sinlat) + (cosmdec * cosmha * coslat));
    // Moon azimuth
    circumstances[42] = Math.atan2(-cosmdec * sinmha, (sinmdec * coslat) - (cosmdec * cosmha * sinlat));
    // Sun apparent radius
    tmp = (circumstances[8] * Math.cos(f1) * Math.sin(f2)) - (circumstances[9] * Math.sin(f1) * Math.cos(f2));
    var R = tmp / (Math.sin(f1) - Math.sin(f2));
    var rs = Math.asin(R / Math.sqrt((u * u) + (v * v) + (zs * zs))); // Topocentric
    circumstances[43] = rs * R2D;
    // Moon apparent radius
    var k = tmp / ((Math.sin(f1) / lambdak1k2) + Math.sin(f2));
    var rm = Math.asin(k / Math.sqrt((u * u) + (v * v) + (zm * zm))); // Topocentric
    circumstances[44] = rm * R2D;
    sunset_alt[0] = circumstances[45] * R2D; // Sun
    sunset_azi[0] = circumstances[46] * R2D;
    if (sunset_azi[0] < 0.0)
        sunset_azi[0] += 360.0;
    else if (sunset_azi[0] >= 360.0)
        sunset_azi[0] -= 360.0;
    sunset_rad[0] = circumstances[43] * 100;
    sunset_alt[1] = circumstances[41] * R2D; // Moon
    sunset_azi[1] = circumstances[42] * R2D;
    if (sunset_azi[1] < 0.0)
        sunset_azi[1] += 360.0;
    else if (sunset_azi[1] >= 360.0)
        sunset_azi[1] -= 360.0;
    sunset_rad[1] = circumstances[44] * 100;

    var jd = getjd(circumstances);
    circumstances[51] = getsn(jd);	// Sun axis from celestial north
}

//
// Return the distance in meters between two locations (reasonable accuracy)
function haversineDistance(lat1, lon1, lat2, lon2)
{
    var dphi = lat2 - lat1;
    var dlambda = lon2 - lon1;

    var a = Math.sin(dphi / 2.0) * Math.sin(dphi / 2.0) + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dlambda / 2.0) * Math.sin(dlambda / 2.0);
    var c = 2.0 * Math.atan2(Math.sqrt(a), Math.sqrt(1.0 - a));

    return (6378137.0 * c);
}

//
// Return the initial bearing in degrees between two locations (reasonable accuracy)
function haversineBearing(lat1, lon1, lat2, lon2)
{
    var dlambda = lon2 - lon1;

    var y = Math.sin(dlambda) * Math.cos(lat2);
    var x = (Math.cos(lat1) * Math.sin(lat2)) - (Math.sin(lat1) * Math.cos(lat2) * Math.cos(dlambda));
    var brng = Math.atan2(y, x) * R2D;
    if (brng < 0.0)
        brng += 360.0;

    return brng;
}

//
// Look for the nearest point on the centerline
function nearestCenterlineDistance(language, lat, lon)
{
    var distStr = "";

    if ( typeof gPolylineCCoords !== "undefined" )
    {
        if (gPolylineCCoords.length > 0)
        {
            var distMin = 1000000000.0;
            var dist, idx, brng;

            for (i = 0; i < gPolylineCCoords.length; i++)
            {
                dist = haversineDistance(lat, lon, gPolylineCCoords[i].lat() * D2R, gPolylineCCoords[i].lng() * D2R);
                if (dist < distMin)
                {
                    distMin = dist;
                    idx = i;
                }
            }
            if (distMin != 1000000000.0)
            {
                distMin /= 1000.0;	// In kilometers
                if (myLanguage == "fr")
                {
                    if (distMin > 100.0)
                        distStr = "<br />Ligne centrale:\xA0" + distMin.toFixed(0) + "km ";
                    else if (distMin < 10.0)
                        distStr = "<br />Ligne centrale:\xA0" + (distMin * 1000.0).toFixed(0) + "m ";
                    else
                        distStr = "<br />Ligne centrale:\xA0" + (distMin.toFixed(1)).replace(/\./, ',') + "km ";
                }
                else
                {
                    if (distMin > 100.0)
                        distStr = "<br />Centerline:\xA0" + distMin.toFixed(0) + "km (" + (distMin / 1.609344).toFixed(0) + "mi) ";
                    else if (distMin < 1.60934)	// Less than one mile
                        distStr = "<br />Centerline:\xA0" + (distMin * 1000.0).toFixed(0) + "m (" + (distMin * 3280.84).toFixed(0) + "ft) ";
                    else if (distMin < 10.0)
                        distStr = "<br />Centerline:\xA0" + (distMin * 1000.0).toFixed(0) + "m (" + (distMin / 1.609344).toFixed(2) + "mi) ";
                    else
                        distStr = "<br />Centerline:\xA0" + distMin.toFixed(1) + "km (" + (distMin / 1.609344).toFixed(1) + "mi) ";
                }
                brng = haversineBearing(lat, lon, gPolylineCCoords[idx].lat() * D2R, gPolylineCCoords[idx].lng() * D2R);
                distStr += headingDirection(language, brng);
            }
        }
    }

    return distStr;
}

//
// Direction of the heading
function headingDirection(language, brng)
{
    var brngStr;

    if ((brng >= 348.75) || (brng < 11.25))
        brngStr = "N";
    else if ((brng >= 11.25) && (brng < 33.75))
        brngStr = "NNE";
    else if ((brng >= 33.75) && (brng < 56.25))
        brngStr = "NE";
    else if ((brng >= 56.25) && (brng < 78.75))
        brngStr = "ENE";
    else if ((brng >= 78.75) && (brng < 101.25))
        brngStr = "E";
    else if ((brng >= 101.25) && (brng < 123.75))
        brngStr = "ESE";
    else if ((brng >= 123.75) && (brng < 146.25))
        brngStr = "SE";
    else if ((brng >= 146.25) && (brng < 168.75))
        brngStr = "SSE";
    else if ((brng >= 168.75) && (brng < 191.25))
        brngStr = "S";
    else if ((brng >= 191.25) && (brng < 213.75))
        brngStr = (language == "fr") ? "SSO" : "SSW";
    else if ((brng >= 213.75) && (brng < 236.25))
        brngStr = (language == "fr") ? "SO" : "SW";
    else if ((brng >= 236.25) && (brng < 258.75))
        brngStr = (language == "fr") ? "OSO" : "WSW";
    else if ((brng >= 258.75) && (brng < 281.25))
        brngStr = (language == "fr") ? "O" : "W";
    else if ((brng >= 281.25) && (brng < 303.75))
        brngStr = (language == "fr") ? "ONO" : "WNW";
    else if ((brng >= 303.75) && (brng < 326.25))
        brngStr = (language == "fr") ? "NO" : "NW";
    else if ((brng >= 326.25) && (brng < 348.75))
        brngStr = (language == "fr") ? "NNO" : "NNW";

    return brngStr;
}

//
// Compute the shadow outline at a given time
function shadowOutlineLowAccuracy(t)
{
    var x, y, d, M, l2, omega, m2, cosQmM, sunBelowHorizon, outlineNbPt;

    if (gShadowOutline)
        gShadowOutline.setMap(null);
    if (gShadowOutlineCoords.length > 0)
        gShadowOutlineCoords.length = 0;
    if (gPolylineShadowOutline)
        gPolylineShadowOutline.setMap(null);
    if (mid[39] < 2)
        return;

    x = elements[6] + (t * (elements[7] + (t * (elements[8] + (elements[9] * t)))));
    y = elements[10] + (t * (elements[11] + (t * (elements[12] + (elements[13] * t)))));
    d = (elements[14] + (t * (elements[15] + (t * elements[16])))) * D2R;
    M = elements[17] + (t * (elements[18] + (t * elements[19])));
    l2 = elements[23] + (t * (elements[24] + (t * elements[25])));
    omega = 1.0 / Math.sqrt(1.0 - (kELLIPTICITY_SQUARRED * Math.cos(d) * Math.cos(d)));

    sunBelowHorizon = -kSUN_RADIUS_DEG;

    m2 = (x * x) + (y * y);
    cosQmM = (m2 + (l2 * l2) - 1.0) / (2.0 * Math.sqrt(m2) * l2);
    if (Math.abs(cosQmM) <= 1.0)  // Two end points to the curve
    {
        var angleM, Q, Q1, Q2;

        angleM = Math.atan2(x, y);
        Q1 = (Math.acos(cosQmM) + angleM) * R2D;
        if (Q1 < 0.0)
            Q1 += 360.0;
        else if (Q1 > 360.0)
            Q1 -= 360.0;
        Q2 = (kM_PI_x2 - Math.acos(cosQmM) + angleM) * R2D;
        if (Q2 < 0.0)
            Q2 += 360.0;
        else if (Q2 > 360.0)
            Q2 -= 360.0;
        if (Q1 > Q2)
        {
            Q = Q1;
            Q1 = Q2;
            Q2 = Q;
        }

        // Determine which of the two sections of the circumference is the appropriate one
        Q = (Q1 + Q2) / 2.0;
        if (checkShadowOutlineSection(Q, x, y, d, M, l2, omega, sunBelowHorizon) == false)
        {
            outlineNbPt = buildShadowOutline(Q2, 360.0, 0, x, y, d, M, l2, omega, sunBelowHorizon);
            outlineNbPt = buildShadowOutline(0.0, Q1, outlineNbPt, x, y, d, M, l2, omega, sunBelowHorizon);
        }
        else
            outlineNbPt = buildShadowOutline(Q1, Q2, 0, x, y, d, M, l2, omega, sunBelowHorizon);
    }
    else  // No end points to the curve
        outlineNbPt = buildShadowOutline(0.0, 360.0, 0, x, y, d, M, l2, omega, sunBelowHorizon);

    if (gShadowOutlineCoords.length > 0)
    {
        gShadowOutline = new google.maps.Polygon({
            path: gShadowOutlineCoords,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 0.7,
            strokeWeight: 1,
            fillColor: "#000000",
            fillOpacity: 0.4,
            clickable: false,
            zIndex: 1
        });

        if (typeof window.LatGEP === "undefined")	// To handle manual maps not built by my engine
            var LatGEP = gLatGEP;
        else
            var LatGEP = window.LatGEP;
        if ((LatGEP < -85.0) && (google.maps.geometry.poly.containsLocation(new google.maps.LatLng(90.0, 0.0), gShadowOutline) == true) && (google.maps.geometry.poly.containsLocation(new google.maps.LatLng(-90.0, 0.0), gShadowOutline) == false))
        {
            // Workaround for the Google Map API v3 polygon fill bug over the South Pole (last tested with 3.26)
            var polygonFOCoords = [
                new google.maps.LatLng(-90.0, -180.0, true),
                new google.maps.LatLng( 90.0, -180.0, true),
                new google.maps.LatLng( 90.0,    0.0, true),
                new google.maps.LatLng( 90.0,  180.0, true),
                new google.maps.LatLng(-90.0,  180.0, true),
                new google.maps.LatLng(-90.0,    0.0, true),
                new google.maps.LatLng(-90.0, -180.0, true)];
            gShadowOutline.setOptions({ paths: [ gShadowOutlineCoords, polygonFOCoords ], strokeWeight: 0 });
            gShadowOutline.setMap(map3);
            gPolylineShadowOutline = new google.maps.Polyline({
                path: gShadowOutlineCoords,
                strokeColor: "#FF0000",
                strokeOpacity: 0.7,
                strokeWeight: 1,
                clickable: false,
                geodesic: true,
                zIndex: 1
            });
            gPolylineShadowOutline.setMap(map3);
        }
        else
            gShadowOutline.setMap(map3);
    }
}

function buildShadowOutline(Q1, Q2, bufferIndex, x, y, d, M, l2, omega, sunBelowHorizon)
{
    var i, j, B, B_Old, delta_B, Q, Qrad, l2p, ksi, eta, eta1, b1, b2, H, phi, lambda, alt, validPt;

    var deltaT = getdTValue(1);
    j = bufferIndex;
    for (Q = Q1; Q <= Q2; Q += kSHADOW_DEGREES_STEPSIZE)
    {
        Qrad = Q * D2R;

        // Iterate for the flattening of the Earth
        validPt = true;
        B = 0.0;
        B_Old = 10.0;
        i = 0;
        do
        {
            l2p = l2 - (B * f2);  // Umbral radius (in earth's radii) in the observer's plane
            ksi = x - (l2p * Math.sin(Qrad));
            eta = y - (l2p * Math.cos(Qrad));
            eta1 = omega * eta;
            B = 1.0 - (ksi * ksi) - (eta1 * eta1);  // Better value of B^2
            if (B >= 0.0)
                B = Math.sqrt(B);
            else  // To allow taking into account the average refraction at low Sun elevations
                B = -Math.sqrt(Math.abs(B));
            /*      else  // No point on Earth
             {
             validPt = false;
             //        i = kITERATION_OUTLINE;
             break;
             }*/
            delta_B = Math.abs(B - B_Old);
            B_Old = B;

            i++;
        }
        while ((delta_B > kEPSILON_OUTLINE) && (i < kITERATION_OUTLINE));

        if (validPt == true)
        {
            b1 = omega * Math.sin(d);
            b2 = kMINOR_MAJOR_RADIUS_RATIO * omega * Math.cos(d);
            H = Math.atan2(ksi, (B * b2) - (eta1 * b1));
            if (H < 0.0)
                H += kM_PI_x2;

            phi = Math.atan2(kLATITUDE_FLATTENING * ((B * b1) + (eta1 * b2)) * Math.sin(H), ksi);
            if (phi > kM_PI_d2)
                phi -= Math.PI;
            else if (phi < -kM_PI_d2)
                phi += Math.PI;
            alt = calcSunAltitude(phi, d, H);
            if (alt >= (5.0 * sunBelowHorizon))  // To take into account the average refraction on the horizon (5 time to get the points below the horizon)
            {
                lambda = M - (H * R2D) - (kSIDEREAL2SOLARTIME * deltaT);
                if (lambda > 180.0)
                {
                    while (lambda > 180.0)
                        lambda -= 360.0;
                }
                else if (lambda < -180.0)
                {
                    while (lambda < -180.0)
                        lambda += 360.0;
                }
                phi *= R2D;

                gShadowOutlineCoords.push(new google.maps.LatLng(phi, -lambda));
                j++;
            }
        }

        if (Q == Q1)  // First iteration, so make sure the next ones occurs on an integer angle
            Q = Math.floor(Q1);
        else if (((Q + kSHADOW_DEGREES_STEPSIZE) > Q2) && (Q != Q2))  // Prepare for the last iteration
            Q = Q2 - kSHADOW_DEGREES_STEPSIZE;
    }

    return j;
}

//
// Check to see if a point is valid or not
function checkShadowOutlineSection(Q, x, y, d, M, l2, omega, sunBelowHorizon)
{
    var i, B, B_Old, delta_B, Qrad, l2p, ksi, eta, eta1, validPt;

    Qrad = Q * D2R;

    // Iterate for the flattening of the Earth
    validPt = true;
    B = 0.0;
    B_Old = 10.0;
    i = 0;
    do
    {
        l2p = l2 - (B * f2);  // Umbral radius (in earth's radii) in the observer's plane
        ksi = x - (l2p * Math.sin(Qrad));
        eta = y - (l2p * Math.cos(Qrad));
        eta1 = omega * eta;
        B = 1.0 - (ksi * ksi) - (eta1 * eta1);  // Better value of B^2
        if (B >= 0.0)
            B = Math.sqrt(B);
        else  // To allow taking into account the average refraction at low Sun elevations
            B = -Math.sqrt(Math.abs(B));
        /*    else  // No point on Earth
         {
         validPt = false;
         //      i = kITERATION_OUTLINE;
         break;
         }*/
        delta_B = Math.abs(B - B_Old);
        B_Old = B;

        i++;
    }
    while ((delta_B > kEPSILON_OUTLINE) && (i < kITERATION_OUTLINE));

    if (validPt == true)
    {
        var b1, b2, H, phi, alt, lastAlt;

        lastAlt = -100.0;
        b1 = omega * Math.sin(d);
        b2 = kMINOR_MAJOR_RADIUS_RATIO * omega * Math.cos(d);
        H = Math.atan2(ksi, (B * b2) - (eta1 * b1));
        if (H < 0.0)
            H += kM_PI_x2;

        phi = Math.atan2(kLATITUDE_FLATTENING * ((B * b1) + (eta1 * b2)) * Math.sin(H), ksi);
        if (phi > kM_PI_d2)
            phi -= Math.PI;
        else if (phi < -kM_PI_d2)
            phi += Math.PI;
        alt = calcSunAltitude(phi, d, H);
        if (alt >= (5.0 * sunBelowHorizon))  // To take into account the average refraction on the horizon (5 time to get the points below the horizon)
        {
            if (alt < sunBelowHorizon)
            {
                if ((lastAlt > alt) && (lastAlt >= sunBelowHorizon) && (lastAlt > -100.0))  // Decreasing Sun (helps refine the on the horizon location)
                    validPt = true;
                else
                    validPt = false;
            }
            lastAlt = alt;
        }
        else
            validPt = false;
    }

    return validPt;
}

//
// Compute the Sun altitude in degrees
function calcSunAltitude(lat, d, H)
{
    var alt = Math.asin((Math.sin(lat) * Math.sin(d)) + (Math.cos(lat) * Math.cos(d) * Math.cos(H))) * R2D;

    return alt;
}
//]]>
//-->