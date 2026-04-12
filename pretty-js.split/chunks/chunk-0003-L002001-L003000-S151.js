    init(e) {
        ! function(e) {
            try {
                return void
                function(e, t, n, r, a, i, o) {
                    t.getElementById("clarity-script") || (e[n] = e[n] || function() {
                        (e[n].q = e[n].q || []).push(arguments)
                    }, (i = t.createElement(r)).async = 1, i.src = "https://www.clarity.ms/tag/" + a + "?ref=npm", i.id = "clarity-script", (o = t.getElementsByTagName(r)[0]).parentNode.insertBefore(i, o))
                }(window, document, "clarity", "script", e)
            } catch (t) {
                return
            }
        }(e)
    },
    setTag(e, t) {
        window.clarity("set", e, t)
    },
    identify(e, t, n, r) {
        window.clarity("identify", e, t, n, r)
    },
    consent(e = !0) {
        window.clarity("consent", e)
    },
    consentV2(e = {
        ad_Storage: "granted",
        analytics_Storage: "granted"
    }) {
        window.clarity("consentv2", e)
    },
    upgrade(e) {
        window.clarity("upgrade", e)
    },
    event(e) {
        window.clarity("event", e)
    }
};
let ao = "https://api.goatar.com",
    io = localStorage.getItem("token"),
    oo = localStorage.getItem("X-Tenant");
const so = () => io,
    lo = async (e, t, n) => {
        const r = await bo.get(ao + e, {
            params: t,
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${so()}`,
                "X-App-Locale": $n.language,
                "X-device": "",
                "Access-Control-Allow-Headers": "Origin, X-Requested-With, Content-Type, Accept",
                "X-Tenant": n?.tenant || oo || "-"
            }
        });
        if (200 === r.status) return r.data
    }, co = async (e, t) => {
        try {
            const n = await bo.post(ao + e, JSON.stringify(t), {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${so()}`,
                    "X-Tenant": oo,
                    "X-App-Locale": $n.language,
                    "X-device": "",
                    "Access-Control-Allow-Headers": "Origin, X-Requested-With, Content-Type, Accept"
                }
            });
            if (200 === n.status || 201 === n.status) return n.data
        } catch (n) {
            throw Lo(n, {}, !0), n
        }
    }, uo = async (e, t) => {
        const n = await bo.put(ao + e, JSON.stringify(t), {
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${so()}`,
                "X-Tenant": oo,
                "X-App-Locale": $n.language,
                "X-device": ""
            }
        });
        if (200 === n.status) return n.data
    }, po = async e => {
        const t = await bo.delete(ao + e, {
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${so()}`,
                "X-Tenant": oo,
                "X-App-Locale": $n.language,
                "X-device": ""
            }
        });
        if (200 !== t.status) {
            const e = await t.data;
            if (e.startsWith("{") && e.endsWith("}")) {
                const t = JSON.parse(e);
                throw Error(t.message)
            }
            throw Error(e)
        }
    }, ho = async () => await (async (e, t) => {
        const n = await wo.get(ao + e, {
            params: t,
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${so()}`,
                "X-App-Locale": $n.language,
                "X-device": "",
                "Access-Control-Allow-Headers": "Origin, X-Requested-With, Content-Type, Accept"
            }
        });
        if (200 === n.status) return n.data
    })("/api-management/countries"), mo = async e => {
        localStorage.setItem("token", e), io = e, bo.defaults.headers.common.Authorization = `Bearer ${e}` || "";
        return (await lo("/tenancy/api/me")).data
    }, fo = async e => await co("/tenancy/api/signup/send-verification", e), go = async e => await co("/tenancy/send-verification", e), yo = async e => await co("/tenancy/api/signup/verify", e), vo = async e => {
        const t = await co("/tenancy/api/signup/create-tenant", e),
            {
                data: n
            } = t;
        return localStorage.setItem("token", n.token), localStorage.setItem("X-Tenant", n.business_name), bo.defaults.headers.common["X-Tenant"] = n.business_name, bo.defaults.headers.common.Authorization = `Bearer ${n.token}`, localStorage.setItem("X-Tenant", n.business_name), localStorage.setItem("token", n.token), io = n.token, oo = n.business_name, t
    }, _o = async e => (await lo("/api-management/rf/statuses", {
        request_history: e.isHistory,
        type: e.type,
        rf_category_id: e.categoryId
    })).data, xo = Qt.create({
        baseURL: "https://api.goatar.com",
        headers: {
            "X-App-Locale": $n.language,
            "X-device": ""
        },
        withCredentials: !0
    });
xo.defaults.headers.common["X-Tenant"] = oo || "", xo.defaults.headers.common.Authorization = `Bearer ${so()}` || "";
const bo = Qt.create({
        baseURL: "https://api.goatar.com/api-management",
        headers: {
            "X-App-Locale": $n.language,
            "X-device": ""
        },
        withCredentials: !0
    }),
    wo = Qt.create({
        baseURL: "https://api.goatar.com/api-management",
        headers: {
            "X-App-Locale": $n.language,
            "X-device": ""
        },
        withCredentials: !0
    }),
    Co = e => {
        bo.interceptors.response.use(function(e) {
            return e
        }, function(t) {
            return t.response && (401 === t.response.status ? (localStorage.setItem("loggedIn", JSON.stringify(!1)), e("/403")) : 429 === t.response.status ? e("/429") : 503 === t.response.status ? 503 === t.response.data.code && e("/maintenance") : 404 === t.response.status && no.emit("not-found-error", t.response)), Promise.reject(t)
        })
    };
bo.defaults.headers.common["X-Tenant"] = oo || "", bo.defaults.headers.common.Authorization = `Bearer ${so()}` || "", bo.defaults.headers.common["X-App-Locale"] = $n.language, bo.defaults.headers["X-App-Locale"] = $n.language, setTimeout(() => {
    bo.defaults.headers.common["X-App-Locale"] = $n.language, bo.defaults.headers["X-App-Locale"] = $n.language
}, 2e3);
const Mo = (e, t) => {
        e && (bo.defaults.headers.common["X-Tenant"] = e || ""), t && (bo.defaults.headers.common.Authorization = `Bearer ${t}` || "")
    },
    So = e => Qt.isAxiosError(e),
    Lo = (e, t = {}, n = !1) => {
        const r = e.response;
        r ? To(r, t, n) : e.request && ko(e.request)
    },
    ko = e => {
        Zi.error("We are facing some issues, please try again after sometime.", {
            toastId: "error"
        })
    },
    To = (e, t, n = !0) => {
        const r = e.status;
        switch (r) {
            case 400:
                jo(e?.data?.errors?.length ? e.data.errors : e.data, r, t, n);
                break;
            case 404:
                no.emit("not-found-error", e), ro.setTag("api_error_404", e.config?.url || "unknown_endpoint");
                break;
            case 401:
                ro.setTag("api_error_401", e.config?.url || "unknown_endpoint");
                break;
            case 422:
                void 0 !== e.data.errors ? (Object.keys(e.data.errors) && (e.data.errors.message = Object.values(e.data.errors).flat()[0]), jo(e.data.errors, r, t, n)) : jo(e.data, r, t, n);
                break;
            case 500:
            case 501:
            case 502:
            case 503:
            case 504:
                ro.setTag("api_error_500:504", e.config?.url || "unknown_endpoint"), e?.data?.message ? jo(e.data, r, t, n) : Eo(r, t);
                break;
            default:
                void 0 !== e.data.errors ? jo(e.data.errors, r, t, n) : jo(e.data, r, t, n)
        }
    },
    jo = (e, t, n, r = !0) => {
        let a = [];
        if (n.setError) {
            const t = n.setError;
            (function(e) {
                const t = [];
                return Object.keys(e || {})?.forEach(n => {
                    "string" == typeof e[n] ? t.push({
                        name: n,
                        type: "SSE",
                        message: e[n]
                    }) : t.push({
                        name: n,
                        type: "SSE",
                        message: e?.[n]?.[0]
                    })
                }), t
            })(e).forEach(({
                name: e,
                message: n,
                type: i
            }) => {
                r || e && i && n && t(e, {
                    type: i,
                    message: n
                }), n && a.push(n)
            })
        }
        r && (a?.length ? Zi.error(a.join(), {
            toastId: a.join()
        }) : e?.message?.length ? Zi.error(e?.message, {
            toastId: e?.message
        }) : Zi.error($n.t("common.somethingWentWrong"), {
            toastId: "common.somethingWentWrong"
        }))
    },
    Eo = (e, t) => {
        Do(e, t.toast) && Zi.error($n.t("common.serviceUnavailable"), {
            toastId: "common.serviceUnavailable"
        })
    },
    Do = (e, t) => void 0 === t || !0 === t || !1 !== t && (void 0 === t[e] || !0 === t[e]),
    Vo = async e => {
        let t = new FormData;
        return t.append("image", e), t.append("name", e.name), await bo.post("/rf/files", t)
    }, Ao = e => bo.delete(`/rf/files/${e}`), Oo = async e => await bo.post(`/rf/modules/change-status/${e}`);
class Po {
    constructor() {
        this.listeners = new Set, this.subscribe = this.subscribe.bind(this)
    }
    subscribe(e) {
        const t = {
            listener: e
        };
        return this.listeners.add(t), this.onSubscribe(), () => {
            this.listeners.delete(t), this.onUnsubscribe()
        }
    }
    hasListeners() {
        return this.listeners.size > 0
    }
    onSubscribe() {}
    onUnsubscribe() {}
}
const Io = "undefined" == typeof window || "Deno" in window;

function Fo() {}

function Ho(e) {
    return "number" == typeof e && e >= 0 && e !== 1 / 0
}

function No(e, t) {
    return e.filter(e => !t.includes(e))
}

function Ro(e, t) {
    return Math.max(e + (t || 0) - Date.now(), 0)
}

function Yo(e, t, n) {
    return es(e) ? "function" == typeof t ? {
        ...n,
        queryKey: e,
        queryFn: t
    } : {
        ...t,
        queryKey: e
    } : e
}

function Bo(e, t, n) {
    return es(e) ? [{
        ...t,
        queryKey: e
    }, n] : [e || {}, t]
}

function zo(e, t) {
    const {
        type: n = "all",
        exact: r,
        fetchStatus: a,
        predicate: i,
        queryKey: o,
        stale: s
    } = e;
    if (es(o))
        if (r) {
            if (t.queryHash !== Wo(o, t.options)) return !1
        } else if (!qo(t.queryKey, o)) return !1;
    if ("all" !== n) {
        const e = t.isActive();
        if ("active" === n && !e) return !1;
        if ("inactive" === n && e) return !1
    }
    return ("boolean" != typeof s || t.isStale() === s) && ((void 0 === a || a === t.state.fetchStatus) && !(i && !i(t)))
}

function Uo(e, t) {
    const {
        exact: n,
        fetching: r,
        predicate: a,
        mutationKey: i
    } = e;
    if (es(i)) {
        if (!t.options.mutationKey) return !1;
        if (n) {
            if (Zo(t.options.mutationKey) !== Zo(i)) return !1
        } else if (!qo(t.options.mutationKey, i)) return !1
    }
    return ("boolean" != typeof r || "loading" === t.state.status === r) && !(a && !a(t))
}

function Wo(e, t) {
    return ((null == t ? void 0 : t.queryKeyHashFn) || Zo)(e)
}

function Zo(e) {
    return JSON.stringify(e, (e, t) => Jo(t) ? Object.keys(t).sort().reduce((e, n) => (e[n] = t[n], e), {}) : t)
}

function qo(e, t) {
    return $o(e, t)
}

function $o(e, t) {
    return e === t || typeof e == typeof t && (!(!e || !t || "object" != typeof e || "object" != typeof t) && !Object.keys(t).some(n => !$o(e[n], t[n])))
}

function Go(e, t, n = 0) {
    if (e === t) return e;
    if (n > 500) return t;
    const r = Qo(e) && Qo(t);
    if (r || Jo(e) && Jo(t)) {
        const a = r ? e.length : Object.keys(e).length,
            i = r ? t : Object.keys(t),
            o = i.length,
            s = r ? [] : {};
        let l = 0;
        for (let d = 0; d < o; d++) {
            const a = r ? d : i[d];
            s[a] = Go(e[a], t[a], n + 1), s[a] === e[a] && l++
        }
        return a === o && l === a ? e : s
    }
    return t
}

function Ko(e, t) {
    if (e && !t || t && !e) return !1;
    for (const n in e)
        if (e[n] !== t[n]) return !1;
    return !0
}

function Qo(e) {
    return Array.isArray(e) && e.length === Object.keys(e).length
}

function Jo(e) {
    if (!Xo(e)) return !1;
    const t = e.constructor;
    if (void 0 === t) return !0;
    const n = t.prototype;
    return !!Xo(n) && !!n.hasOwnProperty("isPrototypeOf")
}

function Xo(e) {
    return "[object Object]" === Object.prototype.toString.call(e)
}

function es(e) {
    return Array.isArray(e)
}

function ts(e) {
    return new Promise(t => {
        setTimeout(t, e)
    })
}

function ns(e) {
    ts(0).then(e)
}

function rs(e, t, n) {
    return null != n.isDataEqual && n.isDataEqual(e, t) ? e : "function" == typeof n.structuralSharing ? n.structuralSharing(e, t) : !1 !== n.structuralSharing ? Go(e, t) : t
}
const as = new class extends Po {
        constructor() {
            super(), this.setup = e => {
                if (!Io && window.addEventListener) {
                    const t = () => e();
                    return window.addEventListener("visibilitychange", t, !1), window.addEventListener("focus", t, !1), () => {
                        window.removeEventListener("visibilitychange", t), window.removeEventListener("focus", t)
                    }
                }
            }
        }
        onSubscribe() {
            this.cleanup || this.setEventListener(this.setup)
        }
        onUnsubscribe() {
            var e;
            this.hasListeners() || (null == (e = this.cleanup) || e.call(this), this.cleanup = void 0)
        }
        setEventListener(e) {
            var t;
            this.setup = e, null == (t = this.cleanup) || t.call(this), this.cleanup = e(e => {
                "boolean" == typeof e ? this.setFocused(e) : this.onFocus()
            })
        }
        setFocused(e) {
            this.focused !== e && (this.focused = e, this.onFocus())
        }
        onFocus() {
            this.listeners.forEach(({
                listener: e
            }) => {
                e()
            })
        }
        isFocused() {
            return "boolean" == typeof this.focused ? this.focused : "undefined" == typeof document || [void 0, "visible", "prerender"].includes(document.visibilityState)
        }
    },
    is = ["online", "offline"];
const os = new class extends Po {
    constructor() {
        super(), this.setup = e => {
            if (!Io && window.addEventListener) {
                const t = () => e();
                return is.forEach(e => {
                    window.addEventListener(e, t, !1)
                }), () => {
                    is.forEach(e => {
                        window.removeEventListener(e, t)
                    })
                }
            }
        }
    }
    onSubscribe() {
        this.cleanup || this.setEventListener(this.setup)
    }
    onUnsubscribe() {
        var e;
        this.hasListeners() || (null == (e = this.cleanup) || e.call(this), this.cleanup = void 0)
    }
    setEventListener(e) {
        var t;
        this.setup = e, null == (t = this.cleanup) || t.call(this), this.cleanup = e(e => {
            "boolean" == typeof e ? this.setOnline(e) : this.onOnline()
        })
    }
    setOnline(e) {
        this.online !== e && (this.online = e, this.onOnline())
    }
    onOnline() {
        this.listeners.forEach(({
            listener: e
        }) => {
            e()
        })
    }
    isOnline() {
        return "boolean" == typeof this.online ? this.online : "undefined" == typeof navigator || void 0 === navigator.onLine || navigator.onLine
    }
};

function ss(e) {
    return Math.min(1e3 * 2 ** e, 3e4)
}

function ls(e) {
    return "online" !== (null != e ? e : "online") || os.isOnline()
}
class ds {
    constructor(e) {
        this.revert = null == e ? void 0 : e.revert, this.silent = null == e ? void 0 : e.silent
    }
}

function cs(e) {
    return e instanceof ds
}

function us(e) {
    let t, n, r, a = !1,
        i = 0,
        o = !1;
    const s = new Promise((e, t) => {
            n = e, r = t
        }),
        l = () => !as.isFocused() || "always" !== e.networkMode && !os.isOnline(),
        d = r => {
            o || (o = !0, null == e.onSuccess || e.onSuccess(r), null == t || t(), n(r))
        },
        c = n => {
            o || (o = !0, null == e.onError || e.onError(n), null == t || t(), r(n))
        },
        u = () => new Promise(n => {
            t = e => {
                const t = o || !l();
                return t && n(e), t
            }, null == e.onPause || e.onPause()
        }).then(() => {
            t = void 0, o || null == e.onContinue || e.onContinue()
        }),
        p = () => {
            if (o) return;
            let t;
            try {
                t = e.fn()
            } catch (n) {
                t = Promise.reject(n)
            }
            Promise.resolve(t).then(d).catch(t => {
                var n, r;
                if (o) return;
                const s = null != (n = e.retry) ? n : 3,
                    d = null != (r = e.retryDelay) ? r : ss,
                    h = "function" == typeof d ? d(i, t) : d,
                    m = !0 === s || "number" == typeof s && i < s || "function" == typeof s && s(i, t);
                !a && m ? (i++, null == e.onFail || e.onFail(i, t), ts(h).then(() => {
                    if (l()) return u()
                }).then(() => {
                    a ? c(t) : p()
                })) : c(t)
            })
        };
    return ls(e.networkMode) ? p() : u().then(p), {
        promise: s,
        cancel: t => {
            o || (c(new ds(t)), null == e.abort || e.abort())
        },
        continue: () => (null == t ? void 0 : t()) ? s : Promise.resolve(),
        cancelRetry: () => {
            a = !0
        },
        continueRetry: () => {
            a = !1
        }
    }
}
const ps = console;
const hs = function() {
    let e = [],
        t = 0,
        n = e => {
            e()
        },
        r = e => {
            e()
        };
    const a = r => {
            t ? e.push(r) : ns(() => {
                n(r)
            })
        },
        i = () => {
            const t = e;
            e = [], t.length && ns(() => {
                r(() => {
                    t.forEach(e => {
                        n(e)
                    })
                })
            })
        };
    return {
        batch: e => {
            let n;
            t++;
            try {
                n = e()
            } finally {
                t--, t || i()
            }
            return n
        },
        batchCalls: e => (...t) => {
            a(() => {
                e(...t)
            })
        },
        schedule: a,
        setNotifyFunction: e => {
            n = e
        },
        setBatchNotifyFunction: e => {
            r = e
        }
    }
}();
class ms {
    destroy() {
        this.clearGcTimeout()
    }
    scheduleGc() {
        this.clearGcTimeout(), Ho(this.cacheTime) && (this.gcTimeout = setTimeout(() => {
            this.optionalRemove()
        }, this.cacheTime))
    }
    updateCacheTime(e) {
        this.cacheTime = Math.max(this.cacheTime || 0, null != e ? e : Io ? 1 / 0 : 3e5)
    }
    clearGcTimeout() {
        this.gcTimeout && (clearTimeout(this.gcTimeout), this.gcTimeout = void 0)
    }
}
class fs extends ms {
    constructor(e) {
        super(), this.abortSignalConsumed = !1, this.defaultOptions = e.defaultOptions, this.setOptions(e.options), this.observers = [], this.cache = e.cache, this.logger = e.logger || ps, this.queryKey = e.queryKey, this.queryHash = e.queryHash, this.initialState = e.state || function(e) {
            const t = "function" == typeof e.initialData ? e.initialData() : e.initialData,
                n = void 0 !== t,
                r = n ? "function" == typeof e.initialDataUpdatedAt ? e.initialDataUpdatedAt() : e.initialDataUpdatedAt : 0;
            return {
                data: t,
                dataUpdateCount: 0,
                dataUpdatedAt: n ? null != r ? r : Date.now() : 0,
                error: null,
                errorUpdateCount: 0,
                errorUpdatedAt: 0,
                fetchFailureCount: 0,
                fetchFailureReason: null,
                fetchMeta: null,
                isInvalidated: !1,
                status: n ? "success" : "loading",
                fetchStatus: "idle"
            }
        }(this.options), this.state = this.initialState, this.scheduleGc()
    }
    get meta() {
        return this.options.meta
    }
    setOptions(e) {
        this.options = {
            ...this.defaultOptions,
            ...e
        }, this.updateCacheTime(this.options.cacheTime)
    }
    optionalRemove() {
        this.observers.length || "idle" !== this.state.fetchStatus || this.cache.remove(this)
    }
    setData(e, t) {
        const n = rs(this.state.data, e, this.options);
        return this.dispatch({
            data: n,
            type: "success",
            dataUpdatedAt: null == t ? void 0 : t.updatedAt,
            manual: null == t ? void 0 : t.manual
        }), n
    }
    setState(e, t) {
        this.dispatch({
            type: "setState",
            state: e,
            setStateOptions: t
        })
    }
    cancel(e) {
        var t;
        const n = this.promise;
        return null == (t = this.retryer) || t.cancel(e), n ? n.then(Fo).catch(Fo) : Promise.resolve()
    }
    destroy() {
        super.destroy(), this.cancel({
            silent: !0
        })
    }
    reset() {
        this.destroy(), this.setState(this.initialState)
    }
    isActive() {
        return this.observers.some(e => !1 !== e.options.enabled)
    }
    isDisabled() {
        return this.getObserversCount() > 0 && !this.isActive()
    }
    isStale() {
        return this.state.isInvalidated || !this.state.dataUpdatedAt || this.observers.some(e => e.getCurrentResult().isStale)
    }
    isStaleByTime(e = 0) {
        return this.state.isInvalidated || !this.state.dataUpdatedAt || !Ro(this.state.dataUpdatedAt, e)
    }
    onFocus() {
        var e;
        const t = this.observers.find(e => e.shouldFetchOnWindowFocus());
        t && t.refetch({
            cancelRefetch: !1
        }), null == (e = this.retryer) || e.continue()
    }
    onOnline() {
        var e;
        const t = this.observers.find(e => e.shouldFetchOnReconnect());
        t && t.refetch({
            cancelRefetch: !1
        }), null == (e = this.retryer) || e.continue()
    }
    addObserver(e) {
        this.observers.includes(e) || (this.observers.push(e), this.clearGcTimeout(), this.cache.notify({
            type: "observerAdded",
            query: this,
            observer: e
        }))
    }
    removeObserver(e) {
        this.observers.includes(e) && (this.observers = this.observers.filter(t => t !== e), this.observers.length || (this.retryer && (this.abortSignalConsumed ? this.retryer.cancel({
            revert: !0
        }) : this.retryer.cancelRetry()), this.scheduleGc()), this.cache.notify({
            type: "observerRemoved",
            query: this,
            observer: e
        }))
    }
    getObserversCount() {
        return this.observers.length
    }
    invalidate() {
        this.state.isInvalidated || this.dispatch({
            type: "invalidate"
        })
    }
    fetch(e, t) {
        var n, r;
        if ("idle" !== this.state.fetchStatus)
            if (this.state.dataUpdatedAt && null != t && t.cancelRefetch) this.cancel({
                silent: !0
            });
            else if (this.promise) {
            var a;
            return null == (a = this.retryer) || a.continueRetry(), this.promise
        }
        if (e && this.setOptions(e), !this.options.queryFn) {
            const e = this.observers.find(e => e.options.queryFn);
            e && this.setOptions(e.options)
        }
        const i = function() {
                if ("function" == typeof AbortController) return new AbortController
            }(),
            o = {
                queryKey: this.queryKey,
                pageParam: void 0,
                meta: this.meta
            },
            s = e => {
                Object.defineProperty(e, "signal", {
                    enumerable: !0,
                    get: () => {
                        if (i) return this.abortSignalConsumed = !0, i.signal
                    }
                })
            };
        s(o);
        const l = {
            fetchOptions: t,
            options: this.options,
            queryKey: this.queryKey,
            state: this.state,
            fetchFn: () => this.options.queryFn ? (this.abortSignalConsumed = !1, this.options.queryFn(o)) : Promise.reject("Missing queryFn for queryKey '" + this.options.queryHash + "'")
        };
        var d;
        (s(l), null == (n = this.options.behavior) || n.onFetch(l), this.revertState = this.state, "idle" === this.state.fetchStatus || this.state.fetchMeta !== (null == (r = l.fetchOptions) ? void 0 : r.meta)) && this.dispatch({
            type: "fetch",
            meta: null == (d = l.fetchOptions) ? void 0 : d.meta
        });
        const c = e => {
            var t, n, r, a;
            (cs(e) && e.silent || this.dispatch({
                type: "error",
                error: e
            }), cs(e)) || (null == (t = (n = this.cache.config).onError) || t.call(n, e, this), null == (r = (a = this.cache.config).onSettled) || r.call(a, this.state.data, e, this));
            this.isFetchingOptimistic || this.scheduleGc(), this.isFetchingOptimistic = !1
        };
        return this.retryer = us({
            fn: l.fetchFn,
            abort: null == i ? void 0 : i.abort.bind(i),
            onSuccess: e => {
                var t, n, r, a;
                void 0 !== e ? (this.setData(e), null == (t = (n = this.cache.config).onSuccess) || t.call(n, e, this), null == (r = (a = this.cache.config).onSettled) || r.call(a, e, this.state.error, this), this.isFetchingOptimistic || this.scheduleGc(), this.isFetchingOptimistic = !1) : c(new Error(this.queryHash + " data is undefined"))
            },
            onError: c,
            onFail: (e, t) => {
                this.dispatch({
                    type: "failed",
                    failureCount: e,
                    error: t
                })
            },
            onPause: () => {
                this.dispatch({
                    type: "pause"
                })
            },
            onContinue: () => {
                this.dispatch({
                    type: "continue"
                })
            },
            retry: l.options.retry,
            retryDelay: l.options.retryDelay,
            networkMode: l.options.networkMode
        }), this.promise = this.retryer.promise, this.promise
    }
    dispatch(e) {
        this.state = (t => {
            var n, r;
            switch (e.type) {
                case "failed":
                    return {
                        ...t, fetchFailureCount: e.failureCount, fetchFailureReason: e.error
                    };
                case "pause":
                    return {
                        ...t, fetchStatus: "paused"
                    };
                case "continue":
                    return {
                        ...t, fetchStatus: "fetching"
                    };
                case "fetch":
                    return {
                        ...t, fetchFailureCount: 0, fetchFailureReason: null, fetchMeta: null != (n = e.meta) ? n : null, fetchStatus: ls(this.options.networkMode) ? "fetching" : "paused", ...!t.dataUpdatedAt && {
                            error: null,
                            status: "loading"
                        }
                    };
                case "success":
                    return {
                        ...t, data: e.data, dataUpdateCount: t.dataUpdateCount + 1, dataUpdatedAt: null != (r = e.dataUpdatedAt) ? r : Date.now(), error: null, isInvalidated: !1, status: "success", ...!e.manual && {
                            fetchStatus: "idle",
                            fetchFailureCount: 0,
                            fetchFailureReason: null
                        }
                    };
                case "error":
                    const a = e.error;
                    return cs(a) && a.revert && this.revertState ? {
                        ...this.revertState,
                        fetchStatus: "idle"
                    } : {
                        ...t,
                        error: a,
                        errorUpdateCount: t.errorUpdateCount + 1,
                        errorUpdatedAt: Date.now(),
                        fetchFailureCount: t.fetchFailureCount + 1,
                        fetchFailureReason: a,
                        fetchStatus: "idle",
                        status: "error"
                    };
                case "invalidate":
                    return {
                        ...t, isInvalidated: !0
                    };
                case "setState":
                    return {
                        ...t, ...e.state
                    }
            }
        })(this.state), hs.batch(() => {
            this.observers.forEach(t => {
                t.onQueryUpdate(e)
            }), this.cache.notify({
                query: this,
                type: "updated",
                action: e
            })
        })
    }
}
class gs extends Po {
    constructor(e) {
        super(), this.config = e || {}, this.queries = [], this.queriesMap = {}
    }
    build(e, t, n) {
        var r;
        const a = t.queryKey,
            i = null != (r = t.queryHash) ? r : Wo(a, t);
        let o = this.get(i);
        return o || (o = new fs({
            cache: this,
            logger: e.getLogger(),
            queryKey: a,
            queryHash: i,
            options: e.defaultQueryOptions(t),
            state: n,
            defaultOptions: e.getQueryDefaults(a)
        }), this.add(o)), o
    }
    add(e) {
        this.queriesMap[e.queryHash] || (this.queriesMap[e.queryHash] = e, this.queries.push(e), this.notify({
            type: "added",
            query: e
        }))
    }
    remove(e) {
        const t = this.queriesMap[e.queryHash];
        t && (e.destroy(), this.queries = this.queries.filter(t => t !== e), t === e && delete this.queriesMap[e.queryHash], this.notify({
            type: "removed",
            query: e
        }))
    }
    clear() {
        hs.batch(() => {
            this.queries.forEach(e => {
                this.remove(e)
            })
        })
    }
    get(e) {
        return this.queriesMap[e]
    }
    getAll() {
        return this.queries
    }
    find(e, t) {
        const [n] = Bo(e, t);
        return void 0 === n.exact && (n.exact = !0), this.queries.find(e => zo(n, e))
    }
    findAll(e, t) {
        const [n] = Bo(e, t);
        return Object.keys(n).length > 0 ? this.queries.filter(e => zo(n, e)) : this.queries
    }
    notify(e) {
        hs.batch(() => {
            this.listeners.forEach(({
                listener: t
            }) => {
                t(e)
            })
        })
    }
    onFocus() {
        hs.batch(() => {
            this.queries.forEach(e => {
                e.onFocus()
            })
        })
    }
    onOnline() {
        hs.batch(() => {
            this.queries.forEach(e => {
                e.onOnline()
            })
        })
    }
}
class ys extends ms {
    constructor(e) {
        super(), this.defaultOptions = e.defaultOptions, this.mutationId = e.mutationId, this.mutationCache = e.mutationCache, this.logger = e.logger || ps, this.observers = [], this.state = e.state || {
            context: void 0,
            data: void 0,
            error: null,
            failureCount: 0,
            failureReason: null,
            isPaused: !1,
            status: "idle",
            variables: void 0
        }, this.setOptions(e.options), this.scheduleGc()
    }
    setOptions(e) {
        this.options = {
            ...this.defaultOptions,
            ...e
        }, this.updateCacheTime(this.options.cacheTime)
    }
    get meta() {
        return this.options.meta
    }
    setState(e) {
        this.dispatch({
            type: "setState",
            state: e
        })
    }
    addObserver(e) {
        this.observers.includes(e) || (this.observers.push(e), this.clearGcTimeout(), this.mutationCache.notify({
            type: "observerAdded",
            mutation: this,
            observer: e