            function ja(e, t, n) {
                var r, a, i, o, s, l = this.eras();
                for (e = e.toUpperCase(), r = 0, a = l.length; r < a; ++r)
                    if (i = l[r].name.toUpperCase(), o = l[r].abbr.toUpperCase(), s = l[r].narrow.toUpperCase(), n) switch (t) {
                        case "N":
                        case "NN":
                        case "NNN":
                            if (o === e) return l[r];
                            break;
                        case "NNNN":
                            if (i === e) return l[r];
                            break;
                        case "NNNNN":
                            if (s === e) return l[r]
                    } else if ([i, o, s].indexOf(e) >= 0) return l[r]
            }

            function Ea(e, t) {
                var n = e.since <= e.until ? 1 : -1;
                return void 0 === t ? r(e.since).year() : r(e.since).year() + (t - e.offset) * n
            }

            function Da() {
                var e, t, n, r = this.localeData().eras();
                for (e = 0, t = r.length; e < t; ++e) {
                    if (n = this.clone().startOf("day").valueOf(), r[e].since <= n && n <= r[e].until) return r[e].name;
                    if (r[e].until <= n && n <= r[e].since) return r[e].name
                }
                return ""
            }

            function Va() {
                var e, t, n, r = this.localeData().eras();
                for (e = 0, t = r.length; e < t; ++e) {
                    if (n = this.clone().startOf("day").valueOf(), r[e].since <= n && n <= r[e].until) return r[e].narrow;
                    if (r[e].until <= n && n <= r[e].since) return r[e].narrow
                }
                return ""
            }

            function Aa() {
                var e, t, n, r = this.localeData().eras();
                for (e = 0, t = r.length; e < t; ++e) {
                    if (n = this.clone().startOf("day").valueOf(), r[e].since <= n && n <= r[e].until) return r[e].abbr;
                    if (r[e].until <= n && n <= r[e].since) return r[e].abbr
                }
                return ""
            }

            function Oa() {
                var e, t, n, a, i = this.localeData().eras();
                for (e = 0, t = i.length; e < t; ++e)
                    if (n = i[e].since <= i[e].until ? 1 : -1, a = this.clone().startOf("day").valueOf(), i[e].since <= a && a <= i[e].until || i[e].until <= a && a <= i[e].since) return (this.year() - r(i[e].since).year()) * n + i[e].offset;
                return this.year()
            }

            function Pa(e) {
                return s(this, "_erasNameRegex") || Ba.call(this), e ? this._erasNameRegex : this._erasRegex
            }

            function Ia(e) {
                return s(this, "_erasAbbrRegex") || Ba.call(this), e ? this._erasAbbrRegex : this._erasRegex
            }

            function Fa(e) {
                return s(this, "_erasNarrowRegex") || Ba.call(this), e ? this._erasNarrowRegex : this._erasRegex
            }

            function Ha(e, t) {
                return t.erasAbbrRegex(e)
            }

            function Na(e, t) {
                return t.erasNameRegex(e)
            }

            function Ra(e, t) {
                return t.erasNarrowRegex(e)
            }

            function Ya(e, t) {
                return t._eraYearOrdinalRegex || ve
            }

            function Ba() {
                var e, t, n, r, a, i = [],
                    o = [],
                    s = [],
                    l = [],
                    d = this.eras();
                for (e = 0, t = d.length; e < t; ++e) n = je(d[e].name), r = je(d[e].abbr), a = je(d[e].narrow), o.push(n), i.push(r), s.push(a), l.push(n), l.push(r), l.push(a);
                this._erasRegex = new RegExp("^(" + l.join("|") + ")", "i"), this._erasNameRegex = new RegExp("^(" + o.join("|") + ")", "i"), this._erasAbbrRegex = new RegExp("^(" + i.join("|") + ")", "i"), this._erasNarrowRegex = new RegExp("^(" + s.join("|") + ")", "i")
            }

            function za(e, t) {
                R(0, [e, e.length], 0, t)
            }

            function Ua(e) {
                return Ka.call(this, e, this.week(), this.weekday() + this.localeData()._week.dow, this.localeData()._week.dow, this.localeData()._week.doy)
            }

            function Wa(e) {
                return Ka.call(this, e, this.isoWeek(), this.isoWeekday(), 1, 4)
            }

            function Za() {
                return Ct(this.year(), 1, 4)
            }

            function qa() {
                return Ct(this.isoWeekYear(), 1, 4)
            }

            function $a() {
                var e = this.localeData()._week;
                return Ct(this.year(), e.dow, e.doy)
            }

            function Ga() {
                var e = this.localeData()._week;
                return Ct(this.weekYear(), e.dow, e.doy)
            }

            function Ka(e, t, n, r, a) {
                var i;
                return null == e ? wt(this, r, a).year : (t > (i = Ct(e, r, a)) && (t = i), Qa.call(this, e, t, n, r, a))
            }

            function Qa(e, t, n, r, a) {
                var i = bt(e, t, n, r, a),
                    o = _t(i.year, 0, i.dayOfYear);
                return this.year(o.getUTCFullYear()), this.month(o.getUTCMonth()), this.date(o.getUTCDate()), this
            }

            function Ja(e) {
                return null == e ? Math.ceil((this.month() + 1) / 3) : this.month(3 * (e - 1) + this.month() % 3)
            }
            R("N", 0, 0, "eraAbbr"), R("NN", 0, 0, "eraAbbr"), R("NNN", 0, 0, "eraAbbr"), R("NNNN", 0, 0, "eraName"), R("NNNNN", 0, 0, "eraNarrow"), R("y", ["y", 1], "yo", "eraYear"), R("y", ["yy", 2], 0, "eraYear"), R("y", ["yyy", 3], 0, "eraYear"), R("y", ["yyyy", 4], 0, "eraYear"), Le("N", Ha), Le("NN", Ha), Le("NNN", Ha), Le("NNNN", Na), Le("NNNNN", Ra), Ae(["N", "NN", "NNN", "NNNN", "NNNNN"], function(e, t, n, r) {
                var a = n._locale.erasParse(e, r, n._strict);
                a ? g(n).era = a : g(n).invalidEra = e
            }), Le("y", ve), Le("yy", ve), Le("yyy", ve), Le("yyyy", ve), Le("yo", Ya), Ae(["y", "yy", "yyy", "yyyy"], Fe), Ae(["yo"], function(e, t, n, r) {
                var a;
                n._locale._eraYearOrdinalRegex && (a = e.match(n._locale._eraYearOrdinalRegex)), n._locale.eraYearOrdinalParse ? t[Fe] = n._locale.eraYearOrdinalParse(e, a) : t[Fe] = parseInt(e, 10)
            }), R(0, ["gg", 2], 0, function() {
                return this.weekYear() % 100
            }), R(0, ["GG", 2], 0, function() {
                return this.isoWeekYear() % 100
            }), za("gggg", "weekYear"), za("ggggg", "weekYear"), za("GGGG", "isoWeekYear"), za("GGGGG", "isoWeekYear"), Le("G", _e), Le("g", _e), Le("GG", pe, le), Le("gg", pe, le), Le("GGGG", ge, ce), Le("gggg", ge, ce), Le("GGGGG", ye, ue), Le("ggggg", ye, ue), Oe(["gggg", "ggggg", "GGGG", "GGGGG"], function(e, t, n, r) {
                t[r.substr(0, 2)] = De(e)
            }), Oe(["gg", "GG"], function(e, t, n, a) {
                t[a] = r.parseTwoDigitYear(e)
            }), R("Q", 0, "Qo", "quarter"), Le("Q", se), Ae("Q", function(e, t) {
                t[He] = 3 * (De(e) - 1)
            }), R("D", ["DD", 2], "Do", "date"), Le("D", pe, Me), Le("DD", pe, le), Le("Do", function(e, t) {
                return e ? t._dayOfMonthOrdinalParse || t._ordinalParse : t._dayOfMonthOrdinalParseLenient
            }), Ae(["D", "DD"], Ne), Ae("Do", function(e, t) {
                t[Ne] = De(e.match(pe)[0])
            });
            var Xa = Ke("Date", !0);

            function ei(e) {
                var t = Math.round((this.clone().startOf("day") - this.clone().startOf("year")) / 864e5) + 1;
                return null == e ? t : this.add(e - t, "d")
            }
            R("DDD", ["DDDD", 3], "DDDo", "dayOfYear"), Le("DDD", fe), Le("DDDD", de), Ae(["DDD", "DDDD"], function(e, t, n) {
                n._dayOfYear = De(e)
            }), R("m", ["mm", 2], 0, "minute"), Le("m", pe, Se), Le("mm", pe, le), Ae(["m", "mm"], Ye);
            var ni = Ke("Minutes", !1);
            R("s", ["ss", 2], 0, "second"), Le("s", pe, Se), Le("ss", pe, le), Ae(["s", "ss"], Be);
            var ri, ai, ii = Ke("Seconds", !1);
            for (R("S", 0, 0, function() {
                    return ~~(this.millisecond() / 100)
                }), R(0, ["SS", 2], 0, function() {
                    return ~~(this.millisecond() / 10)
                }), R(0, ["SSS", 3], 0, "millisecond"), R(0, ["SSSS", 4], 0, function() {
                    return 10 * this.millisecond()
                }), R(0, ["SSSSS", 5], 0, function() {
                    return 100 * this.millisecond()
                }), R(0, ["SSSSSS", 6], 0, function() {
                    return 1e3 * this.millisecond()
                }), R(0, ["SSSSSSS", 7], 0, function() {
                    return 1e4 * this.millisecond()
                }), R(0, ["SSSSSSSS", 8], 0, function() {
                    return 1e5 * this.millisecond()
                }), R(0, ["SSSSSSSSS", 9], 0, function() {
                    return 1e6 * this.millisecond()
                }), Le("S", fe, se), Le("SS", fe, le), Le("SSS", fe, de), ri = "SSSS"; ri.length <= 9; ri += "S") Le(ri, ve);

            function oi(e, t) {
                t[ze] = De(1e3 * ("0." + e))
            }
            for (ri = "S"; ri.length <= 9; ri += "S") Ae(ri, oi);

            function si() {
                return this._isUTC ? "UTC" : ""
            }

            function li() {
                return this._isUTC ? "Coordinated Universal Time" : ""
            }
            ai = Ke("Milliseconds", !1), R("z", 0, 0, "zoneAbbr"), R("zz", 0, 0, "zoneName");
            var di = w.prototype;

            function ci(e) {
                return Gn(1e3 * e)
            }

            function ui() {
                return Gn.apply(null, arguments).parseZone()
            }

            function pi(e) {
                return e
            }
            di.add = Or, di.calendar = Br, di.clone = zr, di.diff = Kr, di.endOf = ya, di.format = ta, di.from = na, di.fromNow = ra, di.to = aa, di.toNow = ia, di.get = Xe, di.invalidAt = La, di.isAfter = Ur, di.isBefore = Wr, di.isBetween = Zr, di.isSame = qr, di.isSameOrAfter = $r, di.isSameOrBefore = Gr, di.isValid = Ma, di.lang = sa, di.locale = oa, di.localeData = la, di.max = Qn, di.min = Kn, di.parsingFlags = Sa, di.set = et, di.startOf = ga, di.subtract = Pr, di.toArray = ba, di.toObject = wa, di.toDate = xa, di.toISOString = Xr, di.inspect = ea, "undefined" != typeof Symbol && null != Symbol.for && (di[Symbol.for("nodejs.util.inspect.custom")] = function() {
                return "Moment<" + this.format() + ">"
            }), di.toJSON = Ca, di.toString = Jr, di.unix = _a, di.valueOf = va, di.creationData = ka, di.eraName = Da, di.eraNarrow = Va, di.eraAbbr = Aa, di.eraYear = Oa, di.year = $e, di.isLeapYear = Ge, di.weekYear = Ua, di.isoWeekYear = Wa, di.quarter = di.quarters = Ja, di.month = ht, di.daysInMonth = mt, di.week = di.weeks = Tt, di.isoWeek = di.isoWeeks = jt, di.weeksInYear = $a, di.weeksInWeekYear = Ga, di.isoWeeksInYear = Za, di.isoWeeksInISOWeekYear = qa, di.date = Xa, di.day = di.days = Ut, di.weekday = Wt, di.isoWeekday = Zt, di.dayOfYear = ei, di.hour = di.hours = rn, di.minute = di.minutes = ni, di.second = di.seconds = ii, di.millisecond = di.milliseconds = ai, di.utcOffset = fr, di.utc = yr, di.local = vr, di.parseZone = _r, di.hasAlignedHourOffset = xr, di.isDST = br, di.isLocal = Cr, di.isUtcOffset = Mr, di.isUtc = Sr, di.isUTC = Sr, di.zoneAbbr = si, di.zoneName = li, di.dates = S("dates accessor is deprecated. Use date instead.", Xa), di.months = S("months accessor is deprecated. Use month instead", ht), di.years = S("years accessor is deprecated. Use year instead", $e), di.zone = S("moment().zone is deprecated, use moment().utcOffset instead. http://momentjs.com/guides/#/warnings/zone/", gr), di.isDSTShifted = S("isDSTShifted is deprecated. See http://momentjs.com/guides/#/warnings/dst-shifted/ for more information", wr);
            var hi = V.prototype;

            function mi(e, t, n, r) {
                var a = vn(),
                    i = m().set(r, t);
                return a[n](i, e)
            }

            function fi(e, t, n) {
                if (c(e) && (t = e, e = void 0), e = e || "", null != t) return mi(e, t, n, "month");
                var r, a = [];
                for (r = 0; r < 12; r++) a[r] = mi(e, r, n, "month");
                return a
            }

            function gi(e, t, n, r) {
                "boolean" == typeof e ? (c(t) && (n = t, t = void 0), t = t || "") : (n = t = e, e = !1, c(t) && (n = t, t = void 0), t = t || "");
                var a, i = vn(),
                    o = e ? i._week.dow : 0,
                    s = [];
                if (null != n) return mi(t, (n + o) % 7, r, "day");
                for (a = 0; a < 7; a++) s[a] = mi(t, (a + o) % 7, r, "day");
                return s
            }

            function yi(e, t) {
                return fi(e, t, "months")
            }

            function vi(e, t) {
                return fi(e, t, "monthsShort")
            }

            function _i(e, t, n) {
                return gi(e, t, n, "weekdays")
            }

            function xi(e, t, n) {
                return gi(e, t, n, "weekdaysShort")
            }

            function bi(e, t, n) {
                return gi(e, t, n, "weekdaysMin")
            }
            hi.calendar = O, hi.longDateFormat = Z, hi.invalidDate = $, hi.ordinal = Q, hi.preparse = pi, hi.postformat = pi, hi.relativeTime = X, hi.pastFuture = ee, hi.set = E, hi.eras = Ta, hi.erasParse = ja, hi.erasConvertYear = Ea, hi.erasAbbrRegex = Ia, hi.erasNameRegex = Pa, hi.erasNarrowRegex = Fa, hi.months = lt, hi.monthsShort = dt, hi.monthsParse = ut, hi.monthsRegex = gt, hi.monthsShortRegex = ft, hi.week = Mt, hi.firstDayOfYear = kt, hi.firstDayOfWeek = Lt, hi.weekdays = Nt, hi.weekdaysMin = Yt, hi.weekdaysShort = Rt, hi.weekdaysParse = zt, hi.weekdaysRegex = qt, hi.weekdaysShortRegex = $t, hi.weekdaysMinRegex = Gt, hi.isPM = tn, hi.meridiem = an, fn("en", {
                eras: [{
                    since: "0001-01-01",
                    until: 1 / 0,
                    offset: 1,
                    name: "Anno Domini",
                    narrow: "AD",
                    abbr: "AD"
                }, {
                    since: "0000-12-31",
                    until: -1 / 0,
                    offset: 1,
                    name: "Before Christ",
                    narrow: "BC",
                    abbr: "BC"
                }],
                dayOfMonthOrdinalParse: /\d{1,2}(th|st|nd|rd)/,
                ordinal: function(e) {
                    var t = e % 10;
                    return e + (1 === De(e % 100 / 10) ? "th" : 1 === t ? "st" : 2 === t ? "nd" : 3 === t ? "rd" : "th")
                }
            }), r.lang = S("moment.lang is deprecated. Use moment.locale instead.", fn), r.langData = S("moment.langData is deprecated. Use moment.localeData instead.", vn);
            var wi = Math.abs;

            function Ci() {
                var e = this._data;
                return this._milliseconds = wi(this._milliseconds), this._days = wi(this._days), this._months = wi(this._months), e.milliseconds = wi(e.milliseconds), e.seconds = wi(e.seconds), e.minutes = wi(e.minutes), e.hours = wi(e.hours), e.months = wi(e.months), e.years = wi(e.years), this
            }

            function Mi(e, t, n, r) {
                var a = Tr(t, n);
                return e._milliseconds += r * a._milliseconds, e._days += r * a._days, e._months += r * a._months, e._bubble()
            }

            function Si(e, t) {
                return Mi(this, e, t, 1)
            }

            function Li(e, t) {
                return Mi(this, e, t, -1)
            }

            function ki(e) {
                return e < 0 ? Math.floor(e) : Math.ceil(e)
            }

            function Ti() {
                var e, t, n, r, a, i = this._milliseconds,
                    o = this._days,
                    s = this._months,
                    l = this._data;
                return i >= 0 && o >= 0 && s >= 0 || i <= 0 && o <= 0 && s <= 0 || (i += 864e5 * ki(Ei(s) + o), o = 0, s = 0), l.milliseconds = i % 1e3, e = Ee(i / 1e3), l.seconds = e % 60, t = Ee(e / 60), l.minutes = t % 60, n = Ee(t / 60), l.hours = n % 24, o += Ee(n / 24), s += a = Ee(ji(o)), o -= ki(Ei(a)), r = Ee(s / 12), s %= 12, l.days = o, l.months = s, l.years = r, this
            }

            function ji(e) {
                return 4800 * e / 146097
            }

            function Ei(e) {
                return 146097 * e / 4800
            }

            function Di(e) {
                if (!this.isValid()) return NaN;
                var t, n, r = this._milliseconds;
                if ("month" === (e = ne(e)) || "quarter" === e || "year" === e) switch (t = this._days + r / 864e5, n = this._months + ji(t), e) {
                    case "month":
                        return n;
                    case "quarter":
                        return n / 3;
                    case "year":
                        return n / 12
                } else switch (t = this._days + Math.round(Ei(this._months)), e) {
                    case "week":
                        return t / 7 + r / 6048e5;
                    case "day":
                        return t + r / 864e5;
                    case "hour":
                        return 24 * t + r / 36e5;
                    case "minute":
                        return 1440 * t + r / 6e4;
                    case "second":
                        return 86400 * t + r / 1e3;
                    case "millisecond":
                        return Math.floor(864e5 * t) + r;
                    default:
                        throw new Error("Unknown unit " + e)
                }
            }

            function Vi(e) {
                return function() {
                    return this.as(e)
                }
            }
            var Ai = Vi("ms"),
                Oi = Vi("s"),
                Pi = Vi("m"),
                Ii = Vi("h"),
                Fi = Vi("d"),
                Hi = Vi("w"),
                Ni = Vi("M"),
                Ri = Vi("Q"),
                Yi = Vi("y"),
                Bi = Ai;

            function zi() {
                return Tr(this)
            }

            function Ui(e) {
                return e = ne(e), this.isValid() ? this[e + "s"]() : NaN
            }

            function Wi(e) {
                return function() {
                    return this.isValid() ? this._data[e] : NaN
                }
            }
            var Zi = Wi("milliseconds"),
                qi = Wi("seconds"),
                $i = Wi("minutes"),
                Gi = Wi("hours"),
                Ki = Wi("days"),
                Qi = Wi("months"),
                Ji = Wi("years");

            function Xi() {
                return Ee(this.days() / 7)
            }
            var eo = Math.round,
                to = {
                    ss: 44,
                    s: 45,
                    m: 45,
                    h: 22,
                    d: 26,
                    w: null,
                    M: 11
                };

            function no(e, t, n, r, a) {
                return a.relativeTime(t || 1, !!n, e, r)
            }

            function ro(e, t, n, r) {
                var a = Tr(e).abs(),
                    i = eo(a.as("s")),
                    o = eo(a.as("m")),
                    s = eo(a.as("h")),
                    l = eo(a.as("d")),
                    d = eo(a.as("M")),
                    c = eo(a.as("w")),
                    u = eo(a.as("y")),
                    p = i <= n.ss && ["s", i] || i < n.s && ["ss", i] || o <= 1 && ["m"] || o < n.m && ["mm", o] || s <= 1 && ["h"] || s < n.h && ["hh", s] || l <= 1 && ["d"] || l < n.d && ["dd", l];
                return null != n.w && (p = p || c <= 1 && ["w"] || c < n.w && ["ww", c]), (p = p || d <= 1 && ["M"] || d < n.M && ["MM", d] || u <= 1 && ["y"] || ["yy", u])[2] = t, p[3] = +e > 0, p[4] = r, no.apply(null, p)
            }

            function ao(e) {
                return void 0 === e ? eo : "function" == typeof e && (eo = e, !0)
            }

            function io(e, t) {
                return void 0 !== to[e] && (void 0 === t ? to[e] : (to[e] = t, "s" === e && (to.ss = t - 1), !0))
            }

            function oo(e, t) {
                if (!this.isValid()) return this.localeData().invalidDate();
                var n, r, a = !1,
                    i = to;
                return "object" == typeof e && (t = e, e = !1), "boolean" == typeof e && (a = e), "object" == typeof t && (i = Object.assign({}, to, t), null != t.s && null == t.ss && (i.ss = t.s - 1)), r = ro(this, !a, i, n = this.localeData()), a && (r = n.pastFuture(+this, r)), n.postformat(r)
            }
            var so = Math.abs;

            function lo(e) {
                return (e > 0) - (e < 0) || +e
            }

            function co() {
                if (!this.isValid()) return this.localeData().invalidDate();
                var e, t, n, r, a, i, o, s, l = so(this._milliseconds) / 1e3,
                    d = so(this._days),
                    c = so(this._months),
                    u = this.asSeconds();
                return u ? (e = Ee(l / 60), t = Ee(e / 60), l %= 60, e %= 60, n = Ee(c / 12), c %= 12, r = l ? l.toFixed(3).replace(/\.?0+$/, "") : "", a = u < 0 ? "-" : "", i = lo(this._months) !== lo(u) ? "-" : "", o = lo(this._days) !== lo(u) ? "-" : "", s = lo(this._milliseconds) !== lo(u) ? "-" : "", a + "P" + (n ? i + n + "Y" : "") + (c ? i + c + "M" : "") + (d ? o + d + "D" : "") + (t || e || l ? "T" : "") + (t ? s + t + "H" : "") + (e ? s + e + "M" : "") + (l ? s + r + "S" : "")) : "P0D"
            }
            var uo = or.prototype;
            return uo.isValid = ar, uo.abs = Ci, uo.add = Si, uo.subtract = Li, uo.as = Di, uo.asMilliseconds = Ai, uo.asSeconds = Oi, uo.asMinutes = Pi, uo.asHours = Ii, uo.asDays = Fi, uo.asWeeks = Hi, uo.asMonths = Ni, uo.asQuarters = Ri, uo.asYears = Yi, uo.valueOf = Bi, uo._bubble = Ti, uo.clone = zi, uo.get = Ui, uo.milliseconds = Zi, uo.seconds = qi, uo.minutes = $i, uo.hours = Gi, uo.days = Ki, uo.weeks = Xi, uo.months = Qi, uo.years = Ji, uo.humanize = oo, uo.toISOString = co, uo.toString = co, uo.toJSON = co, uo.locale = oa, uo.localeData = la, uo.toIsoString = S("toIsoString() is deprecated. Please use toISOString() instead (notice the capitals)", co), uo.lang = sa, R("X", 0, 0, "unix"), R("x", 0, 0, "valueOf"), Le("x", _e), Le("X", we), Ae("X", function(e, t, n) {
                    n._d = new Date(1e3 * parseFloat(e))
                }), Ae("x", function(e, t, n) {
                    n._d = new Date(De(e))
                }),
                //! moment.js
                r.version = "2.30.1", a(Gn), r.fn = di, r.min = Xn, r.max = er, r.now = tr, r.utc = m, r.unix = ci, r.months = yi, r.isDate = u, r.locale = fn, r.invalid = v, r.duration = Tr, r.isMoment = C, r.weekdays = _i, r.parseZone = ui, r.localeData = vn, r.isDuration = sr, r.monthsShort = vi, r.weekdaysMin = bi, r.defineLocale = gn, r.updateLocale = yn, r.locales = _n, r.weekdaysShort = xi, r.normalizeUnits = ne, r.relativeTimeRounding = ao, r.relativeTimeThreshold = io, r.calendarFormat = Yr, r.prototype = di, r.HTML5_FMT = {
                    DATETIME_LOCAL: "YYYY-MM-DDTHH:mm",
                    DATETIME_LOCAL_SECONDS: "YYYY-MM-DDTHH:mm:ss",
                    DATETIME_LOCAL_MS: "YYYY-MM-DDTHH:mm:ss.SSS",
                    DATE: "YYYY-MM-DD",
                    TIME: "HH:mm",
                    TIME_SECONDS: "HH:mm:ss",
                    TIME_MS: "HH:mm:ss.SSS",
                    WEEK: "GGGG-[W]WW",
                    MONTH: "YYYY-MM"
                }, r
        }()
    }(aZ)), aZ.exports
}
var oZ, sZ;

function lZ() {
    const {
        user: t
    } = Qc(), {
        t: n,
        i18n: {
            language: r
        }
    } = Gn();
    nZ.locale(r);
    const a = nZ(new Date(t?.subscription?.trial_ends_at)).to(nZ().toDate(), !0),
        i = nZ(new Date(t?.subscription?.ends_at)).diff(nZ().toDate(), "days"),
        o = !t?.subscription?.trial_ends_at && t?.subscription?.ended && nZ().isAfter(nZ(t?.subscription?.ends_at)),
        s = t?.subscription?.plan_name_without_translate?.name ? JSON.parse(t?.subscription?.plan_name_without_translate?.name)[r] : "",
        l = `${s}:   ${n(1===i?"dashboard.day_remaining_on":"dashboard.days_remaining")}`,
        d = () => "ar" === r ? e.jsxs(Xn, {
            i18nKey: "days_remaining_ar",
            subscriptionRemainingTime: "days",
            children: [s, ": باقي ", {
                subscriptionRemainingTime: i
            }]
        }) : l;
    if (3 !== t?.subscription?.plan_id) return e.jsx(cP, {
        sx: {
            fontSize: 12,
            padding: "4px 16px",
            p: "8px 16px",
            background: "#FCEDC7",
            borderRadius: "8px"
        },
        children: e.jsxs(cP, {
            sx: {
                display: "flex"
            },
            children: [e.jsxs(rP, {
                variant: "subtitle2",
                alignItems: "center",
                sx: {
                    display: "flex",
                    margin: 0,
                    gap: 5,
                    width: "max-content",
                    textTransform: "capitalize"
                },
                children: [e.jsx(dN, {}), t?.subscription?.trial_ends_at ? "ar" === r ? e.jsxs(Xn, {
                    i18nKey: "days_remaining_ar",
                    subscriptionRemainingTime: "days",
                    children: ["باقي ", {
                        trialRemainingTime: a
                    }, " ", n("dashboard.in_your_trial")]
                }) : `${a} ${n("dashboard.days_remaining_on")} ${n("dashboard.in_your_trial")}` : o ? n("subscription_title") : d()]
            }), t?.is_account_admin ? e.jsx(dP, {
                component: "a",
                href: "https://meetings.hubspot.com/haseeb-mohammad/demo",
                target: "_blank",
                warning: !0,
                sx: {
                    ml: 4,
                    py: 1
                },
                children: n("pricing.contact_us")
            }) : ""]
        })
    })
}
oZ || (oZ = 1, sZ = function(e) {
    //! moment.js locale configuration
    var t = {
            1: "١",
            2: "٢",
            3: "٣",
            4: "٤",
            5: "٥",
            6: "٦",
            7: "٧",
            8: "٨",
            9: "٩",
            0: "٠"
        },
        n = {
            "١": "1",
            "٢": "2",
            "٣": "3",
            "٤": "4",
            "٥": "5",
            "٦": "6",
            "٧": "7",
            "٨": "8",
            "٩": "9",
            "٠": "0"
        },
        r = e.defineLocale("ar-sa", {
            months: "يناير_فبراير_مارس_أبريل_مايو_يونيو_يوليو_أغسطس_سبتمبر_أكتوبر_نوفمبر_ديسمبر".split("_"),
            monthsShort: "يناير_فبراير_مارس_أبريل_مايو_يونيو_يوليو_أغسطس_سبتمبر_أكتوبر_نوفمبر_ديسمبر".split("_"),
            weekdays: "الأحد_الإثنين_الثلاثاء_الأربعاء_الخميس_الجمعة_السبت".split("_"),
            weekdaysShort: "أحد_إثنين_ثلاثاء_أربعاء_خميس_جمعة_سبت".split("_"),
            weekdaysMin: "ح_ن_ث_ر_خ_ج_س".split("_"),
            weekdaysParseExact: !0,
            longDateFormat: {
                LT: "HH:mm",
                LTS: "HH:mm:ss",
                L: "DD/MM/YYYY",
                LL: "D MMMM YYYY",
                LLL: "D MMMM YYYY HH:mm",
                LLLL: "dddd D MMMM YYYY HH:mm"
            },
            meridiemParse: /ص|م/,
            isPM: function(e) {
                return "م" === e
            },
            meridiem: function(e, t, n) {
                return e < 12 ? "ص" : "م"
            },
            calendar: {
                sameDay: "[اليوم على الساعة] LT",
                nextDay: "[غدا على الساعة] LT",
                nextWeek: "dddd [على الساعة] LT",
                lastDay: "[أمس على الساعة] LT",
                lastWeek: "dddd [على الساعة] LT",
                sameElse: "L"
            },
            relativeTime: {
                future: "في %s",
                past: "منذ %s",
                s: "ثوان",
                ss: "%d ثانية",
                m: "دقيقة",
                mm: "%d دقائق",
                h: "ساعة",
                hh: "%d ساعات",
                d: "يوم",
                dd: "%d أيام",
                M: "شهر",
                MM: "%d أشهر",
                y: "سنة",
                yy: "%d سنوات"
            },
            preparse: function(e) {
                return e.replace(/[١٢٣٤٥٦٧٨٩٠]/g, function(e) {
                    return n[e]
                }).replace(/،/g, ",")
            },
            postformat: function(e) {
                return e.replace(/\d/g, function(e) {
                    return t[e]
                }).replace(/,/g, "،")
            },
            week: {
                dow: 0,
                doy: 6
            }
        });
    return r
}, sZ(iZ()));
const dZ = (e, t, n) => {
        const r = bh.get(e, t);
        if (r) {
            const e = n?.find(e => void 0 !== r?.id ? e.Iso2 === r?.id : e.Iso2 === r);
            if (e) return {
                id: e.Iso2,
                name: e.Iso2 + " " + e.Dial
            }
        }
        return {
            id: "SA",
            name: "SA 966"
        }
    },
    cZ = e => e?.split("/")?.pop(),
    uZ = e => e ? e?.charAt(0)?.toUpperCase() + e?.slice(1).toLowerCase() : "",
    pZ = (e, t = 20) => e ? e.length > t ? e.slice(0, t) + "..." : e : "---",
    hZ = (e, t = 20) => {
        const n = e?.split(" ");
        return pZ(n?.[0] ? `${uZ(n?.[0])} ${1===n?.length?"":uZ(n[n?.length-1])}` : "", t)
    },
    mZ = e => {
        const t = e;
        return new Promise((e, n) => {
            const r = new FileReader;
            r.onload = t => {
                e(t?.target?.result)
            }, r.onerror = e => {
                n(e)
            }, r.readAsDataURL(t)
        })
    },
    fZ = (e, t) => -1 !== t?.findIndex(e => -1 === e?.id) ? null : t?.map((t, n) => `${e}[${n}]=` + t?.id).join("&"),
    gZ = e => e && (e?.city || e?.district) ? !e?.city && e?.district ? e?.district.name : e?.city && !e?.district ? e?.city.name : `${e?.city?.name||""} - ${e?.district?.name||""}` : "",
    yZ = (e = []) => e?.reduce((e, t) => e.concat(t), []);
let vZ = "https://api.goatar.com";
const _Z = vZ?.includes("staging") || vZ?.includes("dev"),
    xZ = vZ?.includes("api.dev"),
    bZ = {
        [uU.Tenant]: "tenants",
        [uU.Owner]: "owners",
        [uU.Manager]: "admins",
        [uU.ServiceProfessional]: "professionals"
    },
    wZ = (...e) => e.filter(Boolean).map(e => e.trim()).join(" ");

function CZ(e, t) {
    return "en" === t ? 1 === e ? "day" : "days" : "ar" === t ? 0 === e ? "يوم" : 1 === e ? "يوم واحد" : 2 === e ? "يومان" : e >= 3 && e <= 10 ? "أيام" : e >= 11 && e <= 99 ? "يوما" : "يوم" : "days"
}
const MZ = "chunk_reload_attempted",
    SZ = (e, t = 2) => new Promise((n, r) => {
        const a = t => {
            e().then(n).catch(e => {
                if ((e => {
                        if (!(e instanceof Error)) return !1;
                        const t = e.message ?? "";
                        return t.includes("Failed to fetch dynamically imported module") || t.includes("Importing a module script failed") || t.includes("Unable to preload CSS") || /Loading chunk \d+ failed/.test(t) || /Loading CSS chunk \d+ failed/.test(t)
                    })(e)) {
                    return void(sessionStorage.getItem(MZ) ? r(e) : (sessionStorage.setItem(MZ, "1"), window.location.reload()))
                }
                0 === t ? r(e) : setTimeout(() => a(t - 1), 300)
            })
        };
        a(t)
    }),
    LZ = 260,
    kZ = G(Ke, {
        shouldForwardProp: e => "open" !== e
    })(({
        theme: e,
        open: t
    }) => ({
        boxShadow: "none",
        minHeight: "90px",
        maxHeight: "200px",
        justifyContent: "center",
        zIndex: e.zIndex.drawer,
        transition: e.transitions.create(["width", "margin"], {
            easing: e.transitions.easing.sharp,
            duration: e.transitions.duration.leavingScreen
        }),
        ...t && {
            marginLeft: LZ,
            width: "calc(100% - 260px)",
            [e.breakpoints.down("sm")]: {
                marginLeft: 0,
                width: "100%"
            },
            transition: e.transitions.create(["width", "margin"], {
                easing: e.transitions.easing.sharp,
                duration: e.transitions.duration.enteringScreen
            })
        },
        ...!t && {
            marginLeft: "100px",
            width: "calc(100% - 100px)",
            [e.breakpoints.down("sm")]: {
                marginLeft: 0,
                width: "100%"
            },
            transition: e.transitions.create(["width", "margin"], {
                easing: e.transitions.easing.sharp,
                duration: e.transitions.duration.enteringScreen
            })
        }
    }));

function TZ(t) {
    return e.jsx(i, {
        ...t,
        inheritViewBox: !0,
        children: e.jsx("svg", {
            width: "18",
            height: "18",
            viewBox: "0 0 18 18",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M15 15.0019C15 15.2008 14.921 15.3916 14.7803 15.5322C14.6397 15.6729 14.4489 15.7519 14.25 15.7519H3.75C3.55109 15.7519 3.36032 15.6729 3.21967 15.5322C3.07902 15.3916 3 15.2008 3 15.0019V8.25189H0.75L8.49525 1.21089C8.63333 1.08525 8.81331 1.01562 9 1.01562C9.18669 1.01563 9.36667 1.08525 9.50475 1.21089L17.25 8.25189H15V15.0019ZM13.5 14.2519V6.86964L9 2.77914L4.5 6.86964V14.2519H13.5ZM9 12.7519L6.48075 10.2326C6.32405 10.0759 6.19975 9.88991 6.11494 9.68517C6.03014 9.48044 5.98649 9.261 5.98649 9.03939C5.98649 8.81779 6.03014 8.59835 6.11494 8.39361C6.19975 8.18887 6.32405 8.00284 6.48075 7.84614C6.63745 7.68944 6.82348 7.56514 7.02822 7.48034C7.23296 7.39553 7.45239 7.35188 7.674 7.35188C7.89561 7.35188 8.11504 7.39553 8.31978 7.48034C8.52452 7.56514 8.71055 7.68944 8.86725 7.84614L9 7.97889L9.13275 7.84614C9.28945 7.68944 9.47548 7.56514 9.68022 7.48034C9.88496 7.39553 10.1044 7.35188 10.326 7.35188C10.5476 7.35188 10.767 7.39553 10.9718 7.48034C11.1765 7.56514 11.3626 7.68944 11.5192 7.84614C11.676 8.00284 11.8003 8.18887 11.8851 8.39361C11.9699 8.59835 12.0135 8.81779 12.0135 9.03939C12.0135 9.261 11.9699 9.48044 11.8851 9.68517C11.8003 9.88991 11.676 10.0759 11.5192 10.2326L9 12.7519Z",
                fill: "white"
            })
        })
    })
}
const jZ = e => ({
        width: LZ,
        border: "none",
        transition: e.transitions.create("width", {
            easing: e.transitions.easing.sharp,
            duration: e.transitions.duration.enteringScreen
        }),
        overflowX: "hidden",
        padding: "0 1rem 1rem 2rem"
    }),
    EZ = e => ({
        transition: e.transitions.create("width", {
            easing: e.transitions.easing.sharp,
            duration: e.transitions.duration.leavingScreen
        }),
        border: "none",
        overflowX: "hidden",
        width: "100px",
        padding: "2rem"
    }),
    DZ = G(Qe, {
        shouldForwardProp: e => "open" !== e
    })(({
        theme: e,
        open: t
    }) => ({
        width: LZ,
        flexShrink: 0,
        whiteSpace: "nowrap",
        boxSizing: "border-box",
        ...t && {
            width: LZ,
            ...jZ(e),
            "& .MuiDrawer-paper": jZ(e)
        },
        ...!t && {
            width: "100px",
            ...EZ(e),
            "& .MuiDrawer-paper": EZ(e)
        }
    })),
    VZ = G(Qe)(({
        theme: e,
        open: t
    }) => ({
        "& .MuiDrawer-paper": {
            position: "relative",
            whiteSpace: "nowrap",
            border: "none",
            width: LZ,
            boxSizing: "border-box",
            ...!t && {
                overflowX: "hidden",
                width: e.spacing(9)
            }
        }
    }));

function AZ(t) {
    const {
        currentLanguage: n
    } = nu(), {
        CurrentBrand: r
    } = Gc(), a = Ft(), {
        t: i
    } = Gn(), o = JSON.parse(localStorage.getItem("user") || "{}"), l = Ht(), d = Ys(), c = s(), u = ce(c.breakpoints.down("md")), [p, h] = Dt.useState(), [m, f] = Dt.useState(!0), [g, y] = Dt.useState(!1);
    Dt.useEffect(() => {
        ((e, t) => {
            const n = eP(e);
            n && ZO(n, e => {
                t?.(e)
            })
        })(r, e => {
            d.invalidateQueries({
                queryKey: [SF]
            }), d.invalidateQueries({
                queryKey: [LF]
            }), h(e)
        })
    }, []), Dt.useEffect(() => {
        const e = no.on("not-found-error", e => {
            const t = l.pathname,
                n = ["/announcements/{id}", "/requests/{id}", "/visitor-access/visitor-details/{id}", "/bookings/{id}", "/transactions/{id}", "/suggestions/{id}", "/directory/{id}", "/leasing/details/{id}", "/offers/{id}/view", "/settings/forms/preview/{id}", `/contacts/${uU.Tenant}/details/{id}`, `/contacts/${uU.Owner}/details/{id}`, `/contacts/${uU.Manager}/details/{id}`, `/contacts/${uU.ServiceProfessional}/details/{id}`, "/properties-list/unit/details/{id}", "/properties-list/building/details/{id}", "/properties-list/community/details/{id}", "/settings/facility/{id}"].some(n => {
                    const r = n.replace(/{id}/g, "d+"),
                        a = t?.replace(/\d+/g, "d+");
                    return a?.endsWith(r) && 404 === e?.status
                });
            n && y(!0)
        });
        return () => {
            e()
        }
    }, []);
    const {
        window: v
    } = t, [_, x] = Dt.useState(!1), b = () => {
        x(!1), f(!1)
    }, w = "/dashboard/payment" === l?.pathname, {
        renderRelevantDialog: C,
        openRelevantDialog: M
    } = JW(o), S = void 0 !== v ? () => v().document.body : void 0;
    Dt.useEffect(() => {
        M()
    }, []);
    const L = (k = qc[r]?.marketPlaceUrl, _Z ? k?.replace("marketplace", "marketplace.staging") : xZ ? k?.replace("marketplace", "marketplace.dev") : k);
    var k;
    const T = [...Vae.filter(e => e?.children).map(e => e.children)].flat(),
        j = II(T);
    return e.jsxs(cP, {
        sx: {
            display: "flex",
            height: "100vh"
        },
        children: [e.jsx(je, {}), !w && C(), e.jsx(kZ, {
            position: "fixed",
            open: m,
            sx: {
                bgcolor: "background.paper",
                backgroundImage: "none"
            },
            children: e.jsxs(qe, {
                sx: {
                    width: "100%",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "space-between",
                    flexWrap: "wrap",
                    flex: 1,
                    pr: "24px"
                },
                children: [e.jsx(cP, {
                    center: !0,
                    sx: {
                        display: {
                            xs: "block",
                            sm: "none",
                            padding: "0px 16px"
                        }
                    },
                    children: e.jsx(qW, {})
                }), e.jsx(cP, {
                    sx: {
                        display: {
                            xs: "block",
                            sm: "none",
                            position: "absolute",
                            left: "50%",
                            top: "50%",
                            transform: "translate(-50%, -50%)"
                        }
                    },
                    children: e.jsx(hp, {
                        variant: "h5",
                        children: j
                    })
                }), e.jsxs(cP, {
                    sx: {
                        mr: {
                            xs: 0,
                            sm: 12
                        },
                        display: "flex",
                        flex: 1,
                        alignItems: "center",
                        justifyContent: "flex-end"
                    },
                    children: [e.jsxs(cP, {
                        sx: {
                            justifyContent: "flex-end",
                            display: "flex",
                            mr: "48px"
                        },
                        children: [qc[r]?.marketPlaceUrl ? e.jsx(oi, {
                            I: qI.View,
                            this: $I.MarketPlaces,
                            children: e.jsx(dP, {
                                component: "a",
                                href: `${L}`,
                                variant: "contained",
                                target: "_blank",
                                sx: {
                                    marginRight: "16px",
                                    display: {
                                        xs: "none",
                                        sm: "flex"
                                    }
                                },
                                startIcon: e.jsx(TZ, {}),
                                children: i("themarketplace")
                            })
                        }) : e.jsx(e.Fragment, {}), o?.subscription?.plan_id ? e.jsx(lZ, {}) : null]
                    }), e.jsxs(cP, {
                        sx: {
                            display: "flex",
                            alignItems: "center",
                            gap: {
                                xs: "20px",
                                lg: "40px"
                            },
                            justifyContent: "space-between"
                        },
                        children: [e.jsx(cP, {
                            sx: {
                                mr: "28px",
                                display: {
                                    xs: "none",
                                    sm: "block"
                                }
                            },
                            children: e.jsx(UI, {})
                        }), e.jsx(cP, {
                            xcenter: !0,
                            sx: {
                                mr: "20px",
                                alignItems: "center"
                            },
                            children: e.jsx(uW, {})
                        }), e.jsx(cP, {
                            center: !0,
                            sx: {
                                display: {
                                    xs: "none",
                                    sm: "block"
                                }
                            },
                            children: e.jsx(qW, {})
                        }), e.jsx(qe, {
                            component: "img",
                            sx: {
                                width: "60px",
                                maxHeight: "50px",
                                objectFit: "contain",
                                cursor: "pointer",
                                display: {
                                    xs: "none",
                                    sm: "none"
                                }
                            },
                            alt: "The house from the offer.",
                            src: qc[r]?.logoSm,
                            onClick: () => a("")
                        })]
                    })]
                })]
            })
        }), e.jsx(VZ, {
            dir: n.dir,
            container: S,
            variant: "temporary",
            open: _,
            onClose: () => {