    }), AR(0, ["SSSSSSSS", 8], 0, function() {
        return 1e5 * this.millisecond()
    }), AR(0, ["SSSSSSSSS", 9], 0, function() {
        return 1e6 * this.millisecond()
    }), oY("S", KR, BR), oY("SS", KR, zR), oY("SSS", KR, UR), wz = "SSSS"; wz.length <= 9; wz += "S") oY(wz, XR);

function Sz(e, t) {
    t[wY] = cY(1e3 * ("0." + e))
}
for (wz = "S"; wz.length <= 9; wz += "S") pY(wz, Sz);
Cz = TY("Milliseconds", !1), AR("z", 0, 0, "zoneAbbr"), AR("zz", 0, 0, "zoneName");
var Lz = vR.prototype;

function kz(e) {
    return e
}
Lz.add = ez, Lz.calendar = function(e, t) {
    1 === arguments.length && (arguments[0] ? rz(arguments[0]) ? (e = arguments[0], t = void 0) : function(e) {
        var t, n = rR(e) && !iR(e),
            r = !1,
            a = ["sameDay", "nextDay", "lastDay", "nextWeek", "lastWeek", "sameElse"];
        for (t = 0; t < a.length; t += 1) r = r || aR(e, a[t]);
        return n && r
    }(arguments[0]) && (t = arguments[0], e = void 0) : (e = void 0, t = void 0));
    var n = e || AB(),
        r = UB(n, this).startOf("day"),
        a = tR.calendarFormat(this, r) || "sameElse",
        i = t && (SR(t[a]) ? t[a].call(this, n) : t[a]);
    return this.format(i || this.localeData().calendar(a, this, AB(n)))
}, Lz.clone = function() {
    return new vR(this)
}, Lz.diff = function(e, t, n) {
    var r, a, i;
    if (!this.isValid()) return NaN;
    if (!(r = UB(e, this)).isValid()) return NaN;
    switch (a = 6e4 * (r.utcOffset() - this.utcOffset()), t = HR(t)) {
        case "year":
            i = az(this, r) / 12;
            break;
        case "month":
            i = az(this, r);
            break;
        case "quarter":
            i = az(this, r) / 3;
            break;
        case "second":
            i = (this - r) / 1e3;
            break;
        case "minute":
            i = (this - r) / 6e4;
            break;
        case "hour":
            i = (this - r) / 36e5;
            break;
        case "day":
            i = (this - r - a) / 864e5;
            break;
        case "week":
            i = (this - r - a) / 6048e5;
            break;
        default:
            i = this - r
    }
    return n ? i : dY(i)
}, Lz.endOf = function(e) {
    var t, n;
    if (void 0 === (e = HR(e)) || "millisecond" === e || !this.isValid()) return this;
    switch (n = this._isUTC ? mz : hz, e) {
        case "year":
            t = n(this.year() + 1, 0, 1) - 1;
            break;
        case "quarter":
            t = n(this.year(), this.month() - this.month() % 3 + 3, 1) - 1;
            break;
        case "month":
            t = n(this.year(), this.month() + 1, 1) - 1;
            break;
        case "week":
            t = n(this.year(), this.month(), this.date() - this.weekday() + 7) - 1;
            break;
        case "isoWeek":
            t = n(this.year(), this.month(), this.date() - (this.isoWeekday() - 1) + 7) - 1;
            break;
        case "day":
        case "date":
            t = n(this.year(), this.month(), this.date() + 1) - 1;
            break;
        case "hour":
            t = this._d.valueOf(), t += cz - pz(t + (this._isUTC ? 0 : this.utcOffset() * dz), cz) - 1;
            break;
        case "minute":
            t = this._d.valueOf(), t += dz - pz(t, dz) - 1;
            break;
        case "second":
            t = this._d.valueOf(), t += lz - pz(t, lz) - 1
    }
    return this._d.setTime(t), tR.updateOffset(this, !0), this
}, Lz.format = function(e) {
    e || (e = this.isUtc() ? tR.defaultFormatUtc : tR.defaultFormat);
    var t = PR(this, e);
    return this.localeData().postformat(t)
}, Lz.from = function(e, t) {
    return this.isValid() && (_R(e) && e.isValid() || AB(e).isValid()) ? GB({
        to: this,
        from: e
    }).locale(this.locale()).humanize(!t) : this.localeData().invalidDate()
}, Lz.fromNow = function(e) {
    return this.from(AB(), e)
}, Lz.to = function(e, t) {
    return this.isValid() && (_R(e) && e.isValid() || AB(e).isValid()) ? GB({
        from: this,
        to: e
    }).locale(this.locale()).humanize(!t) : this.localeData().invalidDate()
}, Lz.toNow = function(e) {
    return this.to(AB(), e)
}, Lz.get = function(e) {
    return SR(this[e = HR(e)]) ? this[e]() : this
}, Lz.invalidAt = function() {
    return pR(this).overflow
}, Lz.isAfter = function(e, t) {
    var n = _R(e) ? e : AB(e);
    return !(!this.isValid() || !n.isValid()) && ("millisecond" === (t = HR(t) || "millisecond") ? this.valueOf() > n.valueOf() : n.valueOf() < this.clone().startOf(t).valueOf())
}, Lz.isBefore = function(e, t) {
    var n = _R(e) ? e : AB(e);
    return !(!this.isValid() || !n.isValid()) && ("millisecond" === (t = HR(t) || "millisecond") ? this.valueOf() < n.valueOf() : this.clone().endOf(t).valueOf() < n.valueOf())
}, Lz.isBetween = function(e, t, n, r) {
    var a = _R(e) ? e : AB(e),
        i = _R(t) ? t : AB(t);
    return !!(this.isValid() && a.isValid() && i.isValid()) && (("(" === (r = r || "()")[0] ? this.isAfter(a, n) : !this.isBefore(a, n)) && (")" === r[1] ? this.isBefore(i, n) : !this.isAfter(i, n)))
}, Lz.isSame = function(e, t) {
    var n, r = _R(e) ? e : AB(e);
    return !(!this.isValid() || !r.isValid()) && ("millisecond" === (t = HR(t) || "millisecond") ? this.valueOf() === r.valueOf() : (n = r.valueOf(), this.clone().startOf(t).valueOf() <= n && n <= this.clone().endOf(t).valueOf()))
}, Lz.isSameOrAfter = function(e, t) {
    return this.isSame(e, t) || this.isAfter(e, t)
}, Lz.isSameOrBefore = function(e, t) {
    return this.isSame(e, t) || this.isBefore(e, t)
}, Lz.isValid = function() {
    return hR(this)
}, Lz.lang = oz, Lz.locale = iz, Lz.localeData = sz, Lz.max = PB, Lz.min = OB, Lz.parsingFlags = function() {
    return cR({}, pR(this))
}, Lz.set = function(e, t) {
    if ("object" == typeof e) {
        var n, r = function(e) {
                var t, n = [];
                for (t in e) aR(e, t) && n.push({
                    unit: t,
                    priority: RR[t]
                });
                return n.sort(function(e, t) {
                    return e.priority - t.priority
                }), n
            }(e = NR(e)),
            a = r.length;
        for (n = 0; n < a; n++) this[r[n].unit](e[r[n].unit])
    } else if (SR(this[e = HR(e)])) return this[e](t);
    return this
}, Lz.startOf = function(e) {
    var t, n;
    if (void 0 === (e = HR(e)) || "millisecond" === e || !this.isValid()) return this;
    switch (n = this._isUTC ? mz : hz, e) {
        case "year":
            t = n(this.year(), 0, 1);
            break;
        case "quarter":
            t = n(this.year(), this.month() - this.month() % 3, 1);
            break;
        case "month":
            t = n(this.year(), this.month(), 1);
            break;
        case "week":
            t = n(this.year(), this.month(), this.date() - this.weekday());
            break;
        case "isoWeek":
            t = n(this.year(), this.month(), this.date() - (this.isoWeekday() - 1));
            break;
        case "day":
        case "date":
            t = n(this.year(), this.month(), this.date());
            break;
        case "hour":
            t = this._d.valueOf(), t -= pz(t + (this._isUTC ? 0 : this.utcOffset() * dz), cz);
            break;
        case "minute":
            t = this._d.valueOf(), t -= pz(t, dz);
            break;
        case "second":
            t = this._d.valueOf(), t -= pz(t, lz)
    }
    return this._d.setTime(t), tR.updateOffset(this, !0), this
}, Lz.subtract = tz, Lz.toArray = function() {
    var e = this;
    return [e.year(), e.month(), e.date(), e.hour(), e.minute(), e.second(), e.millisecond()]
}, Lz.toObject = function() {
    var e = this;
    return {
        years: e.year(),
        months: e.month(),
        date: e.date(),
        hours: e.hours(),
        minutes: e.minutes(),
        seconds: e.seconds(),
        milliseconds: e.milliseconds()
    }
}, Lz.toDate = function() {
    return new Date(this.valueOf())
}, Lz.toISOString = function(e) {
    if (!this.isValid()) return null;
    var t = !0 !== e,
        n = t ? this.clone().utc() : this;
    return n.year() < 0 || n.year() > 9999 ? PR(n, t ? "YYYYYY-MM-DD[T]HH:mm:ss.SSS[Z]" : "YYYYYY-MM-DD[T]HH:mm:ss.SSSZ") : SR(Date.prototype.toISOString) ? t ? this.toDate().toISOString() : new Date(this.valueOf() + 60 * this.utcOffset() * 1e3).toISOString().replace("Z", PR(n, "Z")) : PR(n, t ? "YYYY-MM-DD[T]HH:mm:ss.SSS[Z]" : "YYYY-MM-DD[T]HH:mm:ss.SSSZ")
}, Lz.inspect = function() {
    if (!this.isValid()) return "moment.invalid(/* " + this._i + " */)";
    var e, t, n, r = "moment",
        a = "";
    return this.isLocal() || (r = 0 === this.utcOffset() ? "moment.utc" : "moment.parseZone", a = "Z"), e = "[" + r + '("]', t = 0 <= this.year() && this.year() <= 9999 ? "YYYY" : "YYYYYY", n = a + '[")]', this.format(e + t + "-MM-DD[T]HH:mm:ss.SSS" + n)
}, "undefined" != typeof Symbol && null != Symbol.for && (Lz[Symbol.for("nodejs.util.inspect.custom")] = function() {
    return "Moment<" + this.format() + ">"
}), Lz.toJSON = function() {
    return this.isValid() ? this.toISOString() : null
}, Lz.toString = function() {
    return this.clone().locale("en").format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ")
}, Lz.unix = function() {
    return Math.floor(this.valueOf() / 1e3)
}, Lz.valueOf = function() {
    return this._d.valueOf() - 6e4 * (this._offset || 0)
}, Lz.creationData = function() {
    return {
        input: this._i,
        format: this._f,
        locale: this._locale,
        isUTC: this._isUTC,
        strict: this._strict
    }
}, Lz.eraName = function() {
    var e, t, n, r = this.localeData().eras();
    for (e = 0, t = r.length; e < t; ++e) {
        if (n = this.clone().startOf("day").valueOf(), r[e].since <= n && n <= r[e].until) return r[e].name;
        if (r[e].until <= n && n <= r[e].since) return r[e].name
    }
    return ""
}, Lz.eraNarrow = function() {
    var e, t, n, r = this.localeData().eras();
    for (e = 0, t = r.length; e < t; ++e) {
        if (n = this.clone().startOf("day").valueOf(), r[e].since <= n && n <= r[e].until) return r[e].narrow;
        if (r[e].until <= n && n <= r[e].since) return r[e].narrow
    }
    return ""
}, Lz.eraAbbr = function() {
    var e, t, n, r = this.localeData().eras();
    for (e = 0, t = r.length; e < t; ++e) {
        if (n = this.clone().startOf("day").valueOf(), r[e].since <= n && n <= r[e].until) return r[e].abbr;
        if (r[e].until <= n && n <= r[e].since) return r[e].abbr
    }
    return ""
}, Lz.eraYear = function() {
    var e, t, n, r, a = this.localeData().eras();
    for (e = 0, t = a.length; e < t; ++e)
        if (n = a[e].since <= a[e].until ? 1 : -1, r = this.clone().startOf("day").valueOf(), a[e].since <= r && r <= a[e].until || a[e].until <= r && r <= a[e].since) return (this.year() - tR(a[e].since).year()) * n + a[e].offset;
    return this.year()
}, Lz.year = kY, Lz.isLeapYear = function() {
    return fY(this.year())
}, Lz.weekYear = function(e) {
    return vz.call(this, e, this.week(), this.weekday() + this.localeData()._week.dow, this.localeData()._week.dow, this.localeData()._week.doy)
}, Lz.isoWeekYear = function(e) {
    return vz.call(this, e, this.isoWeek(), this.isoWeekday(), 1, 4)
}, Lz.quarter = Lz.quarters = function(e) {
    return null == e ? Math.ceil((this.month() + 1) / 3) : this.month(3 * (e - 1) + this.month() % 3)
}, Lz.month = NY, Lz.daysInMonth = function() {
    return DY(this.year(), this.month())
}, Lz.week = Lz.weeks = function(e) {
    var t = this.localeData().week(this);
    return null == e ? t : this.add(7 * (e - t), "d")
}, Lz.isoWeek = Lz.isoWeeks = function(e) {
    var t = WY(this, 1, 4).week;
    return null == e ? t : this.add(7 * (e - t), "d")
}, Lz.weeksInYear = function() {
    var e = this.localeData()._week;
    return ZY(this.year(), e.dow, e.doy)
}, Lz.weeksInWeekYear = function() {
    var e = this.localeData()._week;
    return ZY(this.weekYear(), e.dow, e.doy)
}, Lz.isoWeeksInYear = function() {
    return ZY(this.year(), 1, 4)
}, Lz.isoWeeksInISOWeekYear = function() {
    return ZY(this.isoWeekYear(), 1, 4)
}, Lz.date = xz, Lz.day = Lz.days = function(e) {
    if (!this.isValid()) return null != e ? this : NaN;
    var t = jY(this, "Day");
    return null != e ? (e = function(e, t) {
        return "string" != typeof e ? e : isNaN(e) ? "number" == typeof(e = t.weekdaysParse(e)) ? e : null : parseInt(e, 10)
    }(e, this.localeData()), this.add(e - t, "d")) : t
}, Lz.weekday = function(e) {
    if (!this.isValid()) return null != e ? this : NaN;
    var t = (this.day() + 7 - this.localeData()._week.dow) % 7;
    return null == e ? t : this.add(e - t, "d")
}, Lz.isoWeekday = function(e) {
    if (!this.isValid()) return null != e ? this : NaN;
    if (null != e) {
        var t = function(e, t) {
            return "string" == typeof e ? t.weekdaysParse(e) % 7 || 7 : isNaN(e) ? null : e
        }(e, this.localeData());
        return this.day(this.day() % 7 ? t : t - 7)
    }
    return this.day() || 7
}, Lz.dayOfYear = function(e) {
    var t = Math.round((this.clone().startOf("day") - this.clone().startOf("year")) / 864e5) + 1;
    return null == e ? t : this.add(e - t, "d")
}, Lz.hour = Lz.hours = iB, Lz.minute = Lz.minutes = bz, Lz.second = Lz.seconds = Mz, Lz.millisecond = Lz.milliseconds = Cz, Lz.utcOffset = function(e, t, n) {
    var r, a = this._offset || 0;
    if (!this.isValid()) return null != e ? this : NaN;
    if (null != e) {
        if ("string" == typeof e) {
            if (null === (e = zB(nY, e))) return this
        } else Math.abs(e) < 16 && !n && (e *= 60);
        return !this._isUTC && t && (r = WB(this)), this._offset = e, this._isUTC = !0, null != r && this.add(r, "m"), a !== e && (!t || this._changeInProgress ? XB(this, GB(e - a, "m"), 1, !1) : this._changeInProgress || (this._changeInProgress = !0, tR.updateOffset(this, !0), this._changeInProgress = null)), this
    }
    return this._isUTC ? a : WB(this)
}, Lz.utc = function(e) {
    return this.utcOffset(0, e)
}, Lz.local = function(e) {
    return this._isUTC && (this.utcOffset(0, e), this._isUTC = !1, e && this.subtract(WB(this), "m")), this
}, Lz.parseZone = function() {
    if (null != this._tzm) this.utcOffset(this._tzm, !1, !0);
    else if ("string" == typeof this._i) {
        var e = zB(tY, this._i);
        null != e ? this.utcOffset(e) : this.utcOffset(0, !0)
    }
    return this
}, Lz.hasAlignedHourOffset = function(e) {
    return !!this.isValid() && (e = e ? AB(e).utcOffset() : 0, (this.utcOffset() - e) % 60 == 0)
}, Lz.isDST = function() {
    return this.utcOffset() > this.clone().month(0).utcOffset() || this.utcOffset() > this.clone().month(5).utcOffset()
}, Lz.isLocal = function() {
    return !!this.isValid() && !this._isUTC
}, Lz.isUtcOffset = function() {
    return !!this.isValid() && this._isUTC
}, Lz.isUtc = ZB, Lz.isUTC = ZB, Lz.zoneAbbr = function() {
    return this._isUTC ? "UTC" : ""
}, Lz.zoneName = function() {
    return this._isUTC ? "Coordinated Universal Time" : ""
}, Lz.dates = bR("dates accessor is deprecated. Use date instead.", xz), Lz.months = bR("months accessor is deprecated. Use month instead", NY), Lz.years = bR("years accessor is deprecated. Use year instead", kY), Lz.zone = bR("moment().zone is deprecated, use moment().utcOffset instead. http://momentjs.com/guides/#/warnings/zone/", function(e, t) {
    return null != e ? ("string" != typeof e && (e = -e), this.utcOffset(e, t), this) : -this.utcOffset()
}), Lz.isDSTShifted = bR("isDSTShifted is deprecated. See http://momentjs.com/guides/#/warnings/dst-shifted/ for more information", function() {
    if (!oR(this._isDSTShifted)) return this._isDSTShifted;
    var e, t = {};
    return yR(t, this), (t = DB(t))._a ? (e = t._isUTC ? uR(t._a) : AB(t._a), this._isDSTShifted = this.isValid() && function(e, t) {
        var n, r = Math.min(e.length, t.length),
            a = Math.abs(e.length - t.length),
            i = 0;
        for (n = 0; n < r; n++) cY(e[n]) !== cY(t[n]) && i++;
        return i + a
    }(t._a, e.toArray()) > 0) : this._isDSTShifted = !1, this._isDSTShifted
});
var Tz = kR.prototype;

function jz(e, t, n, r) {
    var a = fB(),
        i = uR().set(r, t);
    return a[n](i, e)
}

function Ez(e, t, n) {
    if (sR(e) && (t = e, e = void 0), e = e || "", null != t) return jz(e, t, n, "month");
    var r, a = [];
    for (r = 0; r < 12; r++) a[r] = jz(e, r, n, "month");
    return a
}

function Dz(e, t, n, r) {
    "boolean" == typeof e ? (sR(t) && (n = t, t = void 0), t = t || "") : (n = t = e, e = !1, sR(t) && (n = t, t = void 0), t = t || "");
    var a, i = fB(),
        o = e ? i._week.dow : 0,
        s = [];
    if (null != n) return jz(t, (n + o) % 7, r, "day");
    for (a = 0; a < 7; a++) s[a] = jz(t, (a + o) % 7, r, "day");
    return s
}
Tz.calendar = function(e, t, n) {
    var r = this._calendar[e] || this._calendar.sameElse;
    return SR(r) ? r.call(t, n) : r
}, Tz.longDateFormat = function(e) {
    var t = this._longDateFormat[e],
        n = this._longDateFormat[e.toUpperCase()];
    return t || !n ? t : (this._longDateFormat[e] = n.match(jR).map(function(e) {
        return "MMMM" === e || "MM" === e || "DD" === e || "dddd" === e ? e.slice(1) : e
    }).join(""), this._longDateFormat[e])
}, Tz.invalidDate = function() {
    return this._invalidDate
}, Tz.ordinal = function(e) {
    return this._ordinal.replace("%d", e)
}, Tz.preparse = kz, Tz.postformat = kz, Tz.relativeTime = function(e, t, n, r) {
    var a = this._relativeTime[n];
    return SR(a) ? a(e, t, n, r) : a.replace(/%d/i, e)
}, Tz.pastFuture = function(e, t) {
    var n = this._relativeTime[e > 0 ? "future" : "past"];
    return SR(n) ? n(t) : n.replace(/%s/i, t)
}, Tz.set = function(e) {
    var t, n;
    for (n in e) aR(e, n) && (SR(t = e[n]) ? this[n] = t : this["_" + n] = t);
    this._config = e, this._dayOfMonthOrdinalParseLenient = new RegExp((this._dayOfMonthOrdinalParse.source || this._ordinalParse.source) + "|" + /\d{1,2}/.source)
}, Tz.eras = function(e, t) {
    var n, r, a, i = this._eras || fB("en")._eras;
    for (n = 0, r = i.length; n < r; ++n) {
        if ("string" == typeof i[n].since) a = tR(i[n].since).startOf("day"), i[n].since = a.valueOf();
        switch (typeof i[n].until) {
            case "undefined":
                i[n].until = 1 / 0;
                break;
            case "string":
                a = tR(i[n].until).startOf("day").valueOf(), i[n].until = a.valueOf()
        }
    }
    return i
}, Tz.erasParse = function(e, t, n) {
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
}, Tz.erasConvertYear = function(e, t) {
    var n = e.since <= e.until ? 1 : -1;
    return void 0 === t ? tR(e.since).year() : tR(e.since).year() + (t - e.offset) * n
}, Tz.erasAbbrRegex = function(e) {
    return aR(this, "_erasAbbrRegex") || gz.call(this), e ? this._erasAbbrRegex : this._erasRegex
}, Tz.erasNameRegex = function(e) {
    return aR(this, "_erasNameRegex") || gz.call(this), e ? this._erasNameRegex : this._erasRegex
}, Tz.erasNarrowRegex = function(e) {
    return aR(this, "_erasNarrowRegex") || gz.call(this), e ? this._erasNarrowRegex : this._erasRegex
}, Tz.months = function(e, t) {
    return e ? nR(this._months) ? this._months[e.month()] : this._months[(this._months.isFormat || OY).test(t) ? "format" : "standalone"][e.month()] : nR(this._months) ? this._months : this._months.standalone
}, Tz.monthsShort = function(e, t) {
    return e ? nR(this._monthsShort) ? this._monthsShort[e.month()] : this._monthsShort[OY.test(t) ? "format" : "standalone"][e.month()] : nR(this._monthsShort) ? this._monthsShort : this._monthsShort.standalone
}, Tz.monthsParse = function(e, t, n) {
    var r, a, i;
    if (this._monthsParseExact) return FY.call(this, e, t, n);
    for (this._monthsParse || (this._monthsParse = [], this._longMonthsParse = [], this._shortMonthsParse = []), r = 0; r < 12; r++) {
        if (a = uR([2e3, r]), n && !this._longMonthsParse[r] && (this._longMonthsParse[r] = new RegExp("^" + this.months(a, "").replace(".", "") + "$", "i"), this._shortMonthsParse[r] = new RegExp("^" + this.monthsShort(a, "").replace(".", "") + "$", "i")), n || this._monthsParse[r] || (i = "^" + this.months(a, "") + "|^" + this.monthsShort(a, ""), this._monthsParse[r] = new RegExp(i.replace(".", ""), "i")), n && "MMMM" === t && this._longMonthsParse[r].test(e)) return r;
        if (n && "MMM" === t && this._shortMonthsParse[r].test(e)) return r;
        if (!n && this._monthsParse[r].test(e)) return r
    }
}, Tz.monthsRegex = function(e) {
    return this._monthsParseExact ? (aR(this, "_monthsRegex") || RY.call(this), e ? this._monthsStrictRegex : this._monthsRegex) : (aR(this, "_monthsRegex") || (this._monthsRegex = IY), this._monthsStrictRegex && e ? this._monthsStrictRegex : this._monthsRegex)
}, Tz.monthsShortRegex = function(e) {
    return this._monthsParseExact ? (aR(this, "_monthsRegex") || RY.call(this), e ? this._monthsShortStrictRegex : this._monthsShortRegex) : (aR(this, "_monthsShortRegex") || (this._monthsShortRegex = PY), this._monthsShortStrictRegex && e ? this._monthsShortStrictRegex : this._monthsShortRegex)
}, Tz.week = function(e) {
    return WY(e, this._week.dow, this._week.doy).week
}, Tz.firstDayOfYear = function() {
    return this._week.doy
}, Tz.firstDayOfWeek = function() {
    return this._week.dow
}, Tz.weekdays = function(e, t) {
    var n = nR(this._weekdays) ? this._weekdays : this._weekdays[e && !0 !== e && this._weekdays.isFormat.test(t) ? "format" : "standalone"];
    return !0 === e ? qY(n, this._week.dow) : e ? n[e.day()] : n
}, Tz.weekdaysMin = function(e) {
    return !0 === e ? qY(this._weekdaysMin, this._week.dow) : e ? this._weekdaysMin[e.day()] : this._weekdaysMin
}, Tz.weekdaysShort = function(e) {
    return !0 === e ? qY(this._weekdaysShort, this._week.dow) : e ? this._weekdaysShort[e.day()] : this._weekdaysShort
}, Tz.weekdaysParse = function(e, t, n) {
    var r, a, i;
    if (this._weekdaysParseExact) return eB.call(this, e, t, n);
    for (this._weekdaysParse || (this._weekdaysParse = [], this._minWeekdaysParse = [], this._shortWeekdaysParse = [], this._fullWeekdaysParse = []), r = 0; r < 7; r++) {
        if (a = uR([2e3, 1]).day(r), n && !this._fullWeekdaysParse[r] && (this._fullWeekdaysParse[r] = new RegExp("^" + this.weekdays(a, "").replace(".", "\\.?") + "$", "i"), this._shortWeekdaysParse[r] = new RegExp("^" + this.weekdaysShort(a, "").replace(".", "\\.?") + "$", "i"), this._minWeekdaysParse[r] = new RegExp("^" + this.weekdaysMin(a, "").replace(".", "\\.?") + "$", "i")), this._weekdaysParse[r] || (i = "^" + this.weekdays(a, "") + "|^" + this.weekdaysShort(a, "") + "|^" + this.weekdaysMin(a, ""), this._weekdaysParse[r] = new RegExp(i.replace(".", ""), "i")), n && "dddd" === t && this._fullWeekdaysParse[r].test(e)) return r;
        if (n && "ddd" === t && this._shortWeekdaysParse[r].test(e)) return r;
        if (n && "dd" === t && this._minWeekdaysParse[r].test(e)) return r;
        if (!n && this._weekdaysParse[r].test(e)) return r
    }
}, Tz.weekdaysRegex = function(e) {
    return this._weekdaysParseExact ? (aR(this, "_weekdaysRegex") || tB.call(this), e ? this._weekdaysStrictRegex : this._weekdaysRegex) : (aR(this, "_weekdaysRegex") || (this._weekdaysRegex = QY), this._weekdaysStrictRegex && e ? this._weekdaysStrictRegex : this._weekdaysRegex)
}, Tz.weekdaysShortRegex = function(e) {
    return this._weekdaysParseExact ? (aR(this, "_weekdaysRegex") || tB.call(this), e ? this._weekdaysShortStrictRegex : this._weekdaysShortRegex) : (aR(this, "_weekdaysShortRegex") || (this._weekdaysShortRegex = JY), this._weekdaysShortStrictRegex && e ? this._weekdaysShortStrictRegex : this._weekdaysShortRegex)
}, Tz.weekdaysMinRegex = function(e) {
    return this._weekdaysParseExact ? (aR(this, "_weekdaysRegex") || tB.call(this), e ? this._weekdaysMinStrictRegex : this._weekdaysMinRegex) : (aR(this, "_weekdaysMinRegex") || (this._weekdaysMinRegex = XY), this._weekdaysMinStrictRegex && e ? this._weekdaysMinStrictRegex : this._weekdaysMinRegex)
}, Tz.isPM = function(e) {
    return "p" === (e + "").toLowerCase().charAt(0)
}, Tz.meridiem = function(e, t, n) {
    return e > 11 ? n ? "pm" : "PM" : n ? "am" : "AM"
}, hB("en", {
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
        return e + (1 === cY(e % 100 / 10) ? "th" : 1 === t ? "st" : 2 === t ? "nd" : 3 === t ? "rd" : "th")
    }
}), tR.lang = bR("moment.lang is deprecated. Use moment.locale instead.", hB), tR.langData = bR("moment.langData is deprecated. Use moment.localeData instead.", fB);
var Vz = Math.abs;

function Az(e, t, n, r) {
    var a = GB(t, n);
    return e._milliseconds += r * a._milliseconds, e._days += r * a._days, e._months += r * a._months, e._bubble()
}

function Oz(e) {
    return e < 0 ? Math.floor(e) : Math.ceil(e)
}

function Pz(e) {
    return 4800 * e / 146097
}

function Iz(e) {
    return 146097 * e / 4800
}

function Fz(e) {
    return function() {
        return this.as(e)
    }
}
var Hz = Fz("ms"),
    Nz = Fz("s"),
    Rz = Fz("m"),
    Yz = Fz("h"),
    Bz = Fz("d"),
    zz = Fz("w"),
    Uz = Fz("M"),
    Wz = Fz("Q"),
    Zz = Fz("y"),
    qz = Hz;

function $z(e) {
    return function() {
        return this.isValid() ? this._data[e] : NaN
    }
}
var Gz = $z("milliseconds"),
    Kz = $z("seconds"),
    Qz = $z("minutes"),
    Jz = $z("hours"),
    Xz = $z("days"),
    eU = $z("months"),
    tU = $z("years");
var nU = Math.round,
    rU = {
        ss: 44,
        s: 45,
        m: 45,
        h: 22,
        d: 26,
        w: null,
        M: 11
    };

function aU(e, t, n, r, a) {
    return a.relativeTime(t || 1, !!n, e, r)
}
var iU = Math.abs;

function oU(e) {
    return (e > 0) - (e < 0) || +e
}

function sU() {
    if (!this.isValid()) return this.localeData().invalidDate();
    var e, t, n, r, a, i, o, s, l = iU(this._milliseconds) / 1e3,
        d = iU(this._days),
        c = iU(this._months),
        u = this.asSeconds();
    return u ? (e = dY(l / 60), t = dY(e / 60), l %= 60, e %= 60, n = dY(c / 12), c %= 12, r = l ? l.toFixed(3).replace(/\.?0+$/, "") : "", a = u < 0 ? "-" : "", i = oU(this._months) !== oU(u) ? "-" : "", o = oU(this._days) !== oU(u) ? "-" : "", s = oU(this._milliseconds) !== oU(u) ? "-" : "", a + "P" + (n ? i + n + "Y" : "") + (c ? i + c + "M" : "") + (d ? o + d + "D" : "") + (t || e || l ? "T" : "") + (t ? s + t + "H" : "") + (e ? s + e + "M" : "") + (l ? s + r + "S" : "")) : "P0D"
}
var lU = HB.prototype;
lU.isValid = function() {
        return this._isValid
    }, lU.abs = function() {
        var e = this._data;
        return this._milliseconds = Vz(this._milliseconds), this._days = Vz(this._days), this._months = Vz(this._months), e.milliseconds = Vz(e.milliseconds), e.seconds = Vz(e.seconds), e.minutes = Vz(e.minutes), e.hours = Vz(e.hours), e.months = Vz(e.months), e.years = Vz(e.years), this
    }, lU.add = function(e, t) {
        return Az(this, e, t, 1)
    }, lU.subtract = function(e, t) {
        return Az(this, e, t, -1)
    }, lU.as = function(e) {
        if (!this.isValid()) return NaN;
        var t, n, r = this._milliseconds;
        if ("month" === (e = HR(e)) || "quarter" === e || "year" === e) switch (t = this._days + r / 864e5, n = this._months + Pz(t), e) {
            case "month":
                return n;
            case "quarter":
                return n / 3;
            case "year":
                return n / 12
        } else switch (t = this._days + Math.round(Iz(this._months)), e) {
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
    }, lU.asMilliseconds = Hz, lU.asSeconds = Nz, lU.asMinutes = Rz, lU.asHours = Yz, lU.asDays = Bz, lU.asWeeks = zz, lU.asMonths = Uz, lU.asQuarters = Wz, lU.asYears = Zz, lU.valueOf = qz, lU._bubble = function() {
        var e, t, n, r, a, i = this._milliseconds,
            o = this._days,
            s = this._months,
            l = this._data;
        return i >= 0 && o >= 0 && s >= 0 || i <= 0 && o <= 0 && s <= 0 || (i += 864e5 * Oz(Iz(s) + o), o = 0, s = 0), l.milliseconds = i % 1e3, e = dY(i / 1e3), l.seconds = e % 60, t = dY(e / 60), l.minutes = t % 60, n = dY(t / 60), l.hours = n % 24, o += dY(n / 24), s += a = dY(Pz(o)), o -= Oz(Iz(a)), r = dY(s / 12), s %= 12, l.days = o, l.months = s, l.years = r, this
    }, lU.clone = function() {
        return GB(this)
    }, lU.get = function(e) {
        return e = HR(e), this.isValid() ? this[e + "s"]() : NaN
    }, lU.milliseconds = Gz, lU.seconds = Kz, lU.minutes = Qz, lU.hours = Jz, lU.days = Xz, lU.weeks = function() {
        return dY(this.days() / 7)
    }, lU.months = eU, lU.years = tU, lU.humanize = function(e, t) {
        if (!this.isValid()) return this.localeData().invalidDate();
        var n, r, a = !1,
            i = rU;
        return "object" == typeof e && (t = e, e = !1), "boolean" == typeof e && (a = e), "object" == typeof t && (i = Object.assign({}, rU, t), null != t.s && null == t.ss && (i.ss = t.s - 1)), r = function(e, t, n, r) {
            var a = GB(e).abs(),
                i = nU(a.as("s")),
                o = nU(a.as("m")),
                s = nU(a.as("h")),
                l = nU(a.as("d")),
                d = nU(a.as("M")),
                c = nU(a.as("w")),
                u = nU(a.as("y")),
                p = i <= n.ss && ["s", i] || i < n.s && ["ss", i] || o <= 1 && ["m"] || o < n.m && ["mm", o] || s <= 1 && ["h"] || s < n.h && ["hh", s] || l <= 1 && ["d"] || l < n.d && ["dd", l];
            return null != n.w && (p = p || c <= 1 && ["w"] || c < n.w && ["ww", c]), (p = p || d <= 1 && ["M"] || d < n.M && ["MM", d] || u <= 1 && ["y"] || ["yy", u])[2] = t, p[3] = +e > 0, p[4] = r, aU.apply(null, p)
        }(this, !a, i, n = this.localeData()), a && (r = n.pastFuture(+this, r)), n.postformat(r)
    }, lU.toISOString = sU, lU.toString = sU, lU.toJSON = sU, lU.locale = iz, lU.localeData = sz, lU.toIsoString = bR("toIsoString() is deprecated. Please use toISOString() instead (notice the capitals)", sU), lU.lang = oz, AR("X", 0, 0, "unix"), AR("x", 0, 0, "valueOf"), oY("x", eY), oY("X", /[+-]?\d+(\.\d{1,3})?/), pY("X", function(e, t, n) {
        n._d = new Date(1e3 * parseFloat(e))
    }), pY("x", function(e, t, n) {
        n._d = new Date(cY(e))
    }),
    //! moment.js
    tR.version = "2.30.1",
    function(e) {
        XN = e
    }(AB), tR.fn = Lz, tR.min = function() {
        return IB("isBefore", [].slice.call(arguments, 0))
    }, tR.max = function() {
        return IB("isAfter", [].slice.call(arguments, 0))
    }, tR.now = function() {
        return Date.now ? Date.now() : +new Date
    }, tR.utc = uR, tR.unix = function(e) {
        return AB(1e3 * e)
    }, tR.months = function(e, t) {
        return Ez(e, t, "months")
    }, tR.isDate = lR, tR.locale = hB, tR.invalid = mR, tR.duration = GB, tR.isMoment = _R, tR.weekdays = function(e, t, n) {
        return Dz(e, t, n, "weekdays")
    }, tR.parseZone = function() {
        return AB.apply(null, arguments).parseZone()
    }, tR.localeData = fB, tR.isDuration = NB, tR.monthsShort = function(e, t) {
        return Ez(e, t, "monthsShort")
    }, tR.weekdaysMin = function(e, t, n) {
        return Dz(e, t, n, "weekdaysMin")
    }, tR.defineLocale = mB, tR.updateLocale = function(e, t) {
        if (null != t) {
            var n, r, a = sB;
            null != lB[e] && null != lB[e].parentLocale ? lB[e].set(LR(lB[e]._config, t)) : (null != (r = pB(e)) && (a = r._config), t = LR(a, t), null == r && (t.abbr = e), (n = new kR(t)).parentLocale = lB[e], lB[e] = n), hB(e)
        } else null != lB[e] && (null != lB[e].parentLocale ? (lB[e] = lB[e].parentLocale, e === hB() && hB(e)) : null != lB[e] && delete lB[e]);
        return lB[e]
    }, tR.locales = function() {
        return wR(lB)
    }, tR.weekdaysShort = function(e, t, n) {
        return Dz(e, t, n, "weekdaysShort")
    }, tR.normalizeUnits = HR, tR.relativeTimeRounding = function(e) {
        return void 0 === e ? nU : "function" == typeof e && (nU = e, !0)
    }, tR.relativeTimeThreshold = function(e, t) {
        return void 0 !== rU[e] && (void 0 === t ? rU[e] : (rU[e] = t, "s" === e && (rU.ss = t - 1), !0))
    }, tR.calendarFormat = function(e, t) {
        var n = e.diff(t, "days", !0);
        return n < -6 ? "sameElse" : n < -1 ? "lastWeek" : n < 0 ? "lastDay" : n < 1 ? "sameDay" : n < 2 ? "nextDay" : n < 7 ? "nextWeek" : "sameElse"
    }, tR.prototype = Lz, tR.HTML5_FMT = {
        DATETIME_LOCAL: "YYYY-MM-DDTHH:mm",
        DATETIME_LOCAL_SECONDS: "YYYY-MM-DDTHH:mm:ss",
        DATETIME_LOCAL_MS: "YYYY-MM-DDTHH:mm:ss.SSS",
        DATE: "YYYY-MM-DD",
        TIME: "HH:mm",
        TIME_SECONDS: "HH:mm:ss",
        TIME_MS: "HH:mm:ss.SSS",
        WEEK: "GGGG-[W]WW",
        MONTH: "YYYY-MM"
    };
const dU = Dt.forwardRef(({
    notificationItem: t
}, n) => {
    const {
        clickHandler: r,
        NotificationIcon: a
    } = JN();
    return e.jsx(Ne, {
        "data-testid": "notification-data",
        role: "listitem",
        "aria-label": "Notification",
        sx: {
            p: "16px",
            borderRadius: "16px",
            mb: "8px",
            cursor: "pointer",
            backgroundColor: t?.read ? "rgba(0, 0, 0, 0.05)" : "initial",
            "&:hover": {
                backgroundColor: "rgba(0, 0, 0, 0.05)",
                transition: "0.2s ease-in"
            }
        },
        ref: n,
        onClick: () => r(t),
        children: e.jsxs(cP, {
            row: !0,
            children: [e.jsx(cP, {
                sx: {
                    backgroundColor: t?.read ? "#CCD9DD" : "#FFD568",
                    borderRadius: "50%",
                    display: "flex",
                    mr: "8px",
                    width: "38px",
                    height: "38px"
                },
                children: e.jsx(cP, {
                    sx: {
                        p: "7px"
                    },
                    children: e.jsx(a, {
                        type: t?.data?.notify_type,
                        sx: {
                            width: "24px",
                            height: "24px",
                            lineHeight: "16px",
                            color: "#FFF"
                        },
                        category: t?.data?.type ?? ""
                    })
                })
            }), e.jsxs(cP, {
                children: [e.jsx(rP, {
                    s: "14",
                    children: t?.data?.title
                }), e.jsx(rP, {
                    s: "14",
                    light: !0,
                    children: t?.text || t?.notification?.body
                }), t?.created_at && e.jsx(rP, {
                    s: "12",
                    gray: !0,
                    light: !0,
                    sx: {
                        textTransform: "uppercase"
                    },
                    children: tR(t?.created_at).format("h:mm a")
                })]
            })]
        })
    }, t?.id)
});

function cU({
    notification: t
}) {
    const {
        t: n,
        i18n: {
            language: r
        }
    } = Gn(), [a, i] = Dt.useState(null);
    return Dt.useEffect(() => {
        i(t)
    }, [t]), e.jsx(Re, {
        anchorOrigin: {
            vertical: "bottom",
            horizontal: "ar" === r ? "right" : "left"
        },
        open: Boolean(a),
        onClose: () => i(null),
        autoHideDuration: 4e3,
        sx: {
            backgroundColor: "white",
            borderRadius: "8px"
        },
        children: e.jsx(dU, {
            notificationItem: a
        })
    })
}
var uU = (e => (e.AccountAdmin = "accountAdmins", e.Admin = "Admins", e.Tenant = "Tenants", e.Owner = "Owners", e.Manager = "Managers", e.ServiceProfessional = "serviceProfessionals", e))(uU || {}),
    pU = (e => (e.HomeRequests = "serviceManagers", e.NeighborhoodRequests = "serviceManagers", e.Marketing = "marketingManagers", e.Accounting = "accountingManagers", e.Leasing = "salesAndLeasingManagers", e))(pU || {}),
    hU = (e => (e.Professional = "professionals", e.Admin = "admins", e.Tenant = "tenants", e.Owner = "owners", e))(hU || {}),
    mU = (e => (e.NEW = "1", e.ASSIGN = "2", e.START = "5", e.COMPLETE = "3", e.CANCEL = "4", e.ACCEPT = "6", e.QUOTE_RAISED = "7", e.QUOTE_ACCEPT = "8", e.QUOTE_REJECT = "9", e.REJECTED = "10", e.CLOSED = "18", e))(mU || {}),
    fU = (e => (e[e.homeServices = 1] = "homeServices", e[e.neighbourhoodServices = 2] = "neighbourhoodServices", e))(fU || {});
const gU = 3,
    yU = {
        PAGE: "page",
        SEARCH: "query",
        CATEGORY: "rf_category_id",
        SORT: "sortDirection",
        SORT_BY: "sortBy",
        STATUS: "rf_status_id",
        COMMUNITY: "rf_community_id",
        SUB_CATEGORY: "rf_sub_category_id",
        TYPE: "rf_type_id",
        IS_HISTORY: "request_history"
    };
var vU = (e => (e.START = "start", e.COMPLETE = "complete", e.CANCELED = "canceled", e.ASSIGNED = "assigned", e.RESOLVED = "resolved", e.ACCEPT = "accept", e.REJECT = "reject", e.QUOTE_RAISED = "quoteRaised", e.QUOTE_ACCEPT = "quoteAccept", e.QUOTE_REJECTED = "quoteRejected", e))(vU || {});
const _U = "neutral",
    xU = "primary",
    bU = "success",
    wU = "danger",
    CU = "warning",
    MU = async ({
        type: e,
        domain: t
    }) => {
        const n = ((e, t) => {
            switch (e) {
                case vU.START:
                    return {
                        rf_request_id: t?.id, before_starting: t?.beforeStarting
                    };
                case vU.COMPLETE:
                    return {
                        rf_request_id: t?.id, reason: t?.reason, after_completing: t?.afterCompleting
                    };
                case vU.ASSIGNED:
                    return {
                        rf_request_id: t?.id, assignee_id: t?.assigneeId
                    };
                case vU.RESOLVED:
                    return {
                        rf_request_id: t?.id, confirmation_code: t?.confirmationCode
                    };
                case vU.ACCEPT:
                    return {
                        rf_request_id: t?.id, complete_msg: t?.completeMsg
                    };
                case vU.REJECT:
                    return {
                        rf_request_id: t?.id
                    };
                case vU.QUOTE_RAISED:
                    return {
                        rf_request_id: t?.id, service_fees: t?.serviceFees, total_fees: t?.totalFees, additional_fees: t?.additionalFees, spare_parts: t?.spareParts
                    };
                case vU.QUOTE_ACCEPT:
                case vU.QUOTE_REJECTED:
                    return {
                        rf_request_id: t?.id
                    };
                default:
                    return {}
            }
        })(e, t);
        return await co(`/api-management/rf/requests/change-status/${e}`, n)
    }, SU = async (e, t) => {
        const n = await lo("/api-management/rf/users/requests/types", {
            is_paginate: 1,
            limit: 20,
            page: t.page,
            query: t.search,
            rf_sub_category_id: e
        });
        return r = n.data, r?.list?.map(e => ({
            id: e.id,
            name: e.name,
            name_ar: e.name_ar,
            name_en: e.name_en,
            fee_type: e.fee_type,
            price: e.price,
            icon_url: e.icon_url?.url,
            description: e.description,
            status: e.status,
            fee_from: e.fee_from,
            fee_to: e.fee_to
        })) ?? [];
        var r
    }, LU = async () => (await lo("/api-management/rf/users/requests/categories?is_paginate=0")).data, kU = async e => {
        try {
            const n = await lo(`/api-management/rf/users/requests/${e}`);
            return t = n.data, {
                id: t?.id,
                status: {
                    id: t?.status?.id ? String(t.status.id) : null,
                    name: t?.status?.name
                },
                info: {
                    ticketId: t?.id,
                    description: t?.description,
                    type: t?.type,
                    subcategory: t?.sub_category,
                    schTime: t?.date_time,
                    community: t?.unit?.community_name ?? t?.community?.name,
                    building: t?.unit?.building_name,
                    unit: t?.unit?.name,
                    confirmation: t?.confirmation_code ? parseInt(t.confirmation_code, 10) : void 0,
                    attachments: t?.attachments,
                    location: t?.maps ? {
                        link: t?.maps?.mapsLink,
                        label: t?.maps?.formattedAddress
                    } : null
                },
                createdAt: t?.created_at,
                start_date: t?.start_date || "",
                resident: {
                    id: t?.resident?.id,
                    name: t?.resident?.name || t?.resident?.primary_user_name,
                    phone: t?.resident?.phone_number
                },
                professional: t?.assignee ? {
                    id: t?.assignee?.id,
                    name: t?.assignee?.name,
                    phone: t?.assignee?.phone_number
                } : null,
                photos: {
                    before: t?.before_starting,
                    after: t?.after_completing
                },
                category: t?.category,
                rating: t?.rate ? {
                    score: t?.rate?.rate ? Number(t.rate.rate) : null,
                    comment: t?.rate?.comment,
                    created_at: t?.rate?.created_at
                } : null,
                quotation: {
                    serviceFees: t?.service_cost,
                    spareParts: t?.spare_parts,
                    additionalFees: t?.additional_fees
                },
                netProfit: {
                    netProfit: t?.net_profit,
                    totalCompanyExpenses: t?.total_company_expenses,
                    totalQuotation: t?.total_quotation
                },
                invoice: t?.invoice_url || "",
                printUrl: null,
                steps: t?.status_steps,
                history: t?.status_histories?.map(e => ({
                    ...e,
                    id: String(e.id)
                }))
            }
        } catch (n) {
            throw n
        }
        var t
    }, TU = async e => (await lo("/api-management/rf/requests/categories", e)).data, jU = async () => (await lo("/api-management/rf/requests/categories")).data, EU = async e => await co("/api-management/request-category", e), DU = async e => await uo("/api-management/request-category", e), VU = async ({
        id: e,
        active_service: t
    }) => await uo(`/api-management/request-sub-category/${e}`, {
        active_service: t
    }), AU = async ({
        id: e,
        active_service: t
    }) => await uo(`/api-management/request-category/${e}`, {
        active_service: t
    }), OU = (e, t) => lo(`/api-management/rf/requests/download/${t}/${e}`);
var PU = (e => (e[e.OFFERS = 1] = "OFFERS", e[e.FACILITIES = 2] = "FACILITIES", e[e.VISITOR = 3] = "VISITOR", e[e.DIRECTORY = 4] = "DIRECTORY", e))(PU || {});
const IU = () => {
        const e = JSON.parse(localStorage.getItem("user") || "{}"),
            t = Ys(),
            {
                data: n
            } = tl([sF], async () => await (async () => {
                const e = await lo("/api-management/rf/modules");
                return e?.data
            })(), {
                select(e) {
                    const t = [0, "offers", "facilities", "visitorAccess", "directories"];
                    return e?.map(e => ({
                        ...e,
                        subject: t[e.id]
                    }))
                },
                staleTime: 6e4
            }),
            {
                mutate: r
            } = nl({
                mutationFn: Oo,
                onSuccess: () => {
                    t.invalidateQueries([sF]), no.emit("force-sidebar-refresh", !0)
                },
                onMutate: e => {
                    const n = t.getQueryData([sF]);
                    return t.setQueryData([sF], t => t?.map(t => (t.id === e && (t.is_active = "1" === t.is_active ? "0" : "1"), t))), a(n), {