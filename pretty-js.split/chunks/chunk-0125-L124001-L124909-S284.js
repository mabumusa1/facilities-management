        children: e.jsxs(ge, {
            children: [D?.map(n => e.jsx(tae, {
                t: t,
                item: n,
                isMobile: i,
                pathname: c,
                search: u,
                selectedTab: h,
                handleClick: w,
                handleNavigate: C,
                getTabColor: M,
                isTabSelected: k,
                checkCollapse: T
            }, n.text)), e.jsx(L, {
                variant: "middle"
            }), j?.filter(e => e.visible).map(n => e.jsx(tae, {
                item: n,
                isMobile: i,
                pathname: c,
                search: u,
                selectedTab: h,
                handleClick: w,
                handleNavigate: C,
                getTabColor: M,
                isTabSelected: k,
                checkCollapse: T,
                t: t,
                onClickOverride: n.onClickOverride
            }, n.key))]
        })
    })
}
const rae = Dt.lazy(() => SZ(() => rr(() => import("./Maintenance-DB_n09TW.js"), __vite__mapDeps([156, 1, 2, 3, 6])))),
    aae = Dt.lazy(() => SZ(() => rr(() => import("./CreateBusinessUsernameAsync-CPYgH5AE.js"), __vite__mapDeps([157, 3, 1, 2, 6])))),
    iae = Dt.lazy(() => SZ(() => rr(() => import("./CreateBusinessUsernameForm-Cin4smtL.js"), __vite__mapDeps([158, 1, 2, 3, 157, 6])))),
    oae = Dt.lazy(() => SZ(() => rr(() => import("./Welcome-CTFpI6J4.js"), __vite__mapDeps([159, 1, 2, 3, 6])))),
    sae = Dt.lazy(() => SZ(() => rr(() => import("./SignupAsync-D9N-n6h7.js"), __vite__mapDeps([160, 2, 1, 3, 6])))),
    lae = Dt.lazy(() => SZ(() => rr(() => import("./SignupForm-GX9Aih5i.js"), __vite__mapDeps([161, 1, 2, 3, 160, 6])))),
    dae = Dt.lazy(() => SZ(() => rr(() => import("./VerifySignupAsync-CerbJLsT.js"), __vite__mapDeps([162, 2, 1, 3, 6])))),
    cae = Dt.lazy(() => SZ(() => rr(() => import("./401-CO18UFTZ.js"), __vite__mapDeps([163, 1, 2, 3])))),
    uae = Dt.lazy(() => SZ(() => rr(() => import("./404-CLs3i9La.js"), __vite__mapDeps([164, 1, 2, 3, 6])))),
    pae = Dt.lazy(() => SZ(() => rr(() => import("./TokenExpired-Cyke-I8o.js"), __vite__mapDeps([165, 1, 2, 3, 6])))),
    hae = Dt.lazy(() => SZ(() => rr(() => import("./429-BLJrIgmk.js"), __vite__mapDeps([166, 1, 2, 3, 6])))),
    mae = Dt.lazy(() => SZ(() => rr(() => import("./home-D-JxUYCo.js"), __vite__mapDeps([167, 1, 2, 3, 168, 169, 6, 170])))),
    fae = Dt.lazy(() => SZ(() => rr(() => import("./About-Dx9XDEKY.js"), __vite__mapDeps([171, 1, 2, 3, 169, 6])))),
    gae = Dt.lazy(() => SZ(() => rr(() => import("./ContactUs-D-2TXWvf.js"), __vite__mapDeps([172, 1, 2, 3, 173, 6])))),
    yae = Dt.lazy(() => SZ(() => rr(() => import("./Pricing-DHThSIrO.js"), __vite__mapDeps([174, 1, 2, 3, 175, 176, 6])))),
    vae = Dt.lazy(() => SZ(() => rr(() => import("./NoAccess-DTIzUbNF.js"), __vite__mapDeps([177, 1, 2, 3, 6])))),
    _ae = Dt.lazy(() => SZ(() => rr(() => import("./LandingLayout-6AJPa_8s.js"), __vite__mapDeps([178, 1, 2, 3, 173, 168, 169, 6])))),
    xae = Dt.lazy(() => SZ(() => rr(() => import("./MoveOut-BnG6IRdV.js"), __vite__mapDeps([179, 1, 2, 3, 102, 101, 104, 105, 37, 6])))),
    bae = Dt.lazy(() => SZ(() => rr(() => import("./Dahia-BkYbPYD8.js"), __vite__mapDeps([180, 1, 2, 3, 6])))),
    wae = Dt.lazy(() => SZ(() => rr(() => import("./Maps-CPt4Y-Rc.js"), __vite__mapDeps([9, 1, 2, 3, 10, 6, 11])))),
    Cae = Dt.lazy(() => SZ(() => rr(() => import("./PayfortPayment-CPN8JzSf.js"), __vite__mapDeps([181, 1, 2, 3, 6])))),
    Mae = Dt.lazy(() => SZ(() => rr(() => import("./ServiceProviderListing-BinHFr1B.js"), __vite__mapDeps([182, 1, 2, 3, 62, 6])))),
    Sae = Dt.lazy(() => SZ(() => rr(() => import("./ServiceProviderDetails-x1ovDRq9.js"), __vite__mapDeps([183, 1, 2, 3])))),
    Lae = Dt.lazy(() => SZ(() => rr(() => import("./SendInvite-Bdj5lxKx.js"), __vite__mapDeps([184, 1, 2, 3, 62, 6])))),
    kae = Dt.lazy(() => SZ(() => rr(() => import("./OfferDetails-B2qKkuk2.js"), __vite__mapDeps([185, 1, 2, 3, 62, 6])))),
    Tae = Dt.lazy(() => SZ(() => rr(() => import("./QRScan-C-mDU84S.js"), __vite__mapDeps([186, 1, 2, 3, 6])))),
    jae = Dt.lazy(() => SZ(() => rr(() => import("./SMSViewer-DsIynlhZ.js"), __vite__mapDeps([187, 1, 2, 3, 6, 188])))),
    Eae = Dt.lazy(() => SZ(() => rr(() => import("./UpgradePlan-BwFozRLj.js"), __vite__mapDeps([189, 1, 2, 3, 175, 176, 6])))),
    Dae = Dt.lazy(() => SZ(() => rr(() => import("./PaymentRedirect.temp-COojAU46.js"), __vite__mapDeps([190, 1, 2, 3])))),
    Vae = [{
        title: "Visitor Access",
        path: "qr",
        element: e.jsx(Tae, {})
    }, {
        title: "",
        path: "sadeq/:tenant/:id",
        element: e.jsx(jae, {})
    }, {
        title: "Landing",
        path: "",
        element: e.jsx(e.Fragment, {
            children: e.jsx(W2, {
                theme: "navy",
                children: e.jsx(_ae, {})
            })
        }),
        nav: !0,
        children: [{
            title: "Landing",
            path: "/",
            element: e.jsx(mae, {})
        }]
    }, {
        title: "Home",
        path: "",
        element: e.jsx(e.Fragment, {
            children: e.jsx(W2, {
                theme: "light",
                children: e.jsx(_ae, {})
            })
        }),
        nav: !0,
        children: [{
            title: "pricing",
            path: "pricing",
            element: e.jsx(yae, {})
        }, {
            title: "about",
            path: "about",
            element: e.jsx(fae, {})
        }, {
            title: "contact",
            path: "contact",
            element: e.jsx(gae, {})
        }]
    }, {
        title: "Auth",
        path: "",
        element: e.jsx(i0, {
            children: e.jsx(Zt, {})
        }),
        nav: !0,
        children: [{
            title: "No-Access",
            path: "no-access",
            element: e.jsx(vae, {})
        }, {
            title: "login",
            path: "login",
            element: e.jsx(y0, {
                children: ({
                    onSubmit: t,
                    form: n,
                    recaptchaRef: r
                }) => e.jsx(P0, {
                    form: n,
                    onSubmit: t,
                    recaptchaRef: r
                })
            })
        }, {
            title: "signup",
            path: "signup",
            element: e.jsx(sae, {
                children: ({
                    onSubmit: t,
                    form: n,
                    recaptchaRef: r
                }) => e.jsx(lae, {
                    form: n,
                    onSubmit: t,
                    recaptchaRef: r
                })
            })
        }, {
            title: "verify",
            path: "verify",
            element: e.jsx(F0, {
                children: ({
                    form: t,
                    handleVerify: n,
                    recaptchaRef: r
                }) => e.jsx(B0, {
                    onresendOTP: go,
                    form: t,
                    onVerify: n,
                    recaptchaRef: r
                })
            })
        }, {
            title: "verify-code",
            path: "verifycode",
            element: e.jsx(dae, {
                children: ({
                    form: t,
                    handleVerify: n,
                    recaptchaRef: r
                }) => e.jsx(B0, {
                    onresendOTP: fo,
                    form: t,
                    onVerify: n,
                    recaptchaRef: r
                })
            })
        }, {
            title: "create-business-username",
            path: "create-business-username",
            element: e.jsx(aae, {
                children: ({
                    onSubmit: t,
                    form: n
                }) => e.jsx(iae, {
                    form: n,
                    onSubmit: t
                })
            })
        }]
    }, {
        title: "protected",
        path: "",
        element: e.jsx(HZ, {
            children: e.jsx(Zt, {})
        }),
        nav: !0,
        children: [{
            title: "Welcome",
            path: "welcome",
            element: e.jsx(oae, {})
        }, {
            title: "select-plan-new",
            path: "select-plan-new",
            element: e.jsx(yae, {})
        }]
    }, {
        title: "",
        path: "pay-redirect",
        element: e.jsx(Dae, {})
    }, {
        title: "design-system",
        path: "system",
        element: e.jsx(ere, {})
    }, {
        title: "forms",
        path: "forms",
        element: e.jsx(ire, {})
    }, {
        title: "dashboard",
        path: "",
        element: e.jsx(o0, {
            children: e.jsx(AZ, {})
        }),
        nav: !0,
        children: [{
            title: "Add location",
            path: "maps",
            element: e.jsx(wae, {})
        }, {
            title: "Add location",
            path: "pay",
            element: e.jsx(Cae, {})
        }, {
            title: "dashboard",
            path: "dashboard",
            element: e.jsx(S2, {})
        }, {
            title: "dashboard",
            path: "dashboard",
            children: [...AJ, ...FZ, {
                title: "Expired-Leases",
                path: "expiring-leases",
                element: e.jsx(M4, {}),
                nav: !0
            }, {
                title: "View-expired-lease",
                path: "expiring-leases/:id",
                element: e.jsx(Xre, {}),
                nav: !0
            }, {
                title: "payment_page_title",
                path: "payment",
                element: e.jsx(Eae, {})
            }, {
                title: "move-out-tenants",
                path: "move-out-tenants",
                element: e.jsx(Zt, {}),
                children: [{
                    title: "move-out-tenants",
                    path: "",
                    element: e.jsx(xae, {})
                }, {
                    title: "serviceRequests",
                    path: ":type/details/:id/active-requests",
                    element: e.jsx(i4, {})
                }]
            }, {
                title: "service-provider",
                path: "service-provider",
                element: e.jsx(Zt, {}),
                children: [{
                    title: "service-provider",
                    path: "",
                    element: e.jsx(Mae, {})
                }, {
                    title: "service-provider-details",
                    path: "details/:id",
                    element: e.jsx(Sae, {})
                }, {
                    title: "serviceproviderOfferdetails",
                    path: "details/:id/:offerId",
                    element: e.jsx(kae, {})
                }, {
                    title: "sendInvite",
                    path: "sendInvite",
                    element: e.jsx(Lae, {})
                }]
            }, {
                title: "system-reports",
                path: "system-reports",
                element: e.jsx(Zt, {}),
                children: [{
                    title: "reports",
                    path: "",
                    element: e.jsx(L2, {})
                }, {
                    title: "lease-statement",
                    path: "Lease",
                    element: e.jsx(P2, {})
                }, {
                    title: "maintenance-request",
                    path: "maintenance",
                    element: e.jsx(N2, {})
                }]
            }, {
                title: "power-bi-reports",
                path: "power-bi-reports",
                element: e.jsx(bee, {})
            }, ...ZZ, ...d4, ...FJ, ...BZ]
        }, {
            title: "notifications",
            path: "notifications",
            element: e.jsx(R2, {})
        }, ...$5, ...WJ, ...m0, ...Qre, ...a0, ...c0, ...J1, ...GZ, ...YJ, ...aee, ...Jne, ...Ire, {
            title: "more",
            path: "more",
            element: e.jsx(nae, {})
        }]
    }, {
        title: "404",
        path: "401",
        element: e.jsx(cae, {})
    }, {
        title: "401",
        path: "403",
        element: e.jsx(pae, {})
    }, {
        title: "429",
        path: "429",
        element: e.jsx(hae, {})
    }, {
        title: "404",
        path: "*",
        element: e.jsx(uae, {})
    }, {
        title: "maintenance",
        path: "maintenance",
        element: e.jsx(rae, {})
    }, {
        title: "Dahia terms and conditions",
        path: "TermsConditionsDahia",
        element: e.jsx(bae, {})
    }];

function Aae() {
    const {
        pathname: e
    } = Ht(), t = Ft();
    return Dt.useEffect(() => {
        window.scrollTo(0, 0), Co(t)
    }, [e]), null
}

function Oae() {
    return navigator.onLine
}

function Pae() {
    const [t, n] = Dt.useState(!1), {
        t: r
    } = Gn(), a = Dt.useSyncExternalStore(e => {
        const t = function(e) {
            return window.addEventListener("online", e), window.addEventListener("offline", e), () => {
                window.removeEventListener("online", e), window.removeEventListener("offline", e)
            }
        }(() => {
            n(!0), e()
        });
        return () => t()
    }, Oae), i = () => {
        n(!1)
    };
    return e.jsx(Re, {
        open: t,
        autoHideDuration: 3e3,
        onClose: i,
        anchorOrigin: {
            vertical: "bottom",
            horizontal: "center"
        },
        children: e.jsx(Et, {
            onClose: i,
            severity: a ? "success" : "error",
            sx: {
                width: "100%"
            },
            children: r(a ? "onlineMsg" : "offlineMsg")
        })
    })
}
const Iae = window.location.host,
    Fae = Iae.split(".")[0],
    Hae = Iae.split(".")[1],
    Nae = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => rc), void 0).then(e => ({
        default: e.IntercomProvider
    })))),
    Rae = new class {
        constructor(e = {}) {
            this.queryCache = e.queryCache || new gs, this.mutationCache = e.mutationCache || new vs, this.logger = e.logger || ps, this.defaultOptions = e.defaultOptions || {}, this.queryDefaults = [], this.mutationDefaults = [], this.mountCount = 0
        }
        mount() {
            this.mountCount++, 1 === this.mountCount && (this.unsubscribeFocus = as.subscribe(() => {
                as.isFocused() && (this.resumePausedMutations(), this.queryCache.onFocus())
            }), this.unsubscribeOnline = os.subscribe(() => {
                os.isOnline() && (this.resumePausedMutations(), this.queryCache.onOnline())
            }))
        }
        unmount() {
            var e, t;
            this.mountCount--, 0 === this.mountCount && (null == (e = this.unsubscribeFocus) || e.call(this), this.unsubscribeFocus = void 0, null == (t = this.unsubscribeOnline) || t.call(this), this.unsubscribeOnline = void 0)
        }
        isFetching(e, t) {
            const [n] = Bo(e, t);
            return n.fetchStatus = "fetching", this.queryCache.findAll(n).length
        }
        isMutating(e) {
            return this.mutationCache.findAll({
                ...e,
                fetching: !0
            }).length
        }
        getQueryData(e, t) {
            var n;
            return null == (n = this.queryCache.find(e, t)) ? void 0 : n.state.data
        }
        ensureQueryData(e, t, n) {
            const r = Yo(e, t, n),
                a = this.getQueryData(r.queryKey);
            return a ? Promise.resolve(a) : this.fetchQuery(r)
        }
        getQueriesData(e) {
            return this.getQueryCache().findAll(e).map(({
                queryKey: e,
                state: t
            }) => [e, t.data])
        }
        setQueryData(e, t, n) {
            const r = this.queryCache.find(e),
                a = function(e, t) {
                    return "function" == typeof e ? e(t) : e
                }(t, null == r ? void 0 : r.state.data);
            if (void 0 === a) return;
            const i = Yo(e),
                o = this.defaultQueryOptions(i);
            return this.queryCache.build(this, o).setData(a, {
                ...n,
                manual: !0
            })
        }
        setQueriesData(e, t, n) {
            return hs.batch(() => this.getQueryCache().findAll(e).map(({
                queryKey: e
            }) => [e, this.setQueryData(e, t, n)]))
        }
        getQueryState(e, t) {
            var n;
            return null == (n = this.queryCache.find(e, t)) ? void 0 : n.state
        }
        removeQueries(e, t) {
            const [n] = Bo(e, t), r = this.queryCache;
            hs.batch(() => {
                r.findAll(n).forEach(e => {
                    r.remove(e)
                })
            })
        }
        resetQueries(e, t, n) {
            const [r, a] = Bo(e, t, n), i = this.queryCache, o = {
                type: "active",
                ...r
            };
            return hs.batch(() => (i.findAll(r).forEach(e => {
                e.reset()
            }), this.refetchQueries(o, a)))
        }
        cancelQueries(e, t, n) {
            const [r, a = {}] = Bo(e, t, n);
            void 0 === a.revert && (a.revert = !0);
            const i = hs.batch(() => this.queryCache.findAll(r).map(e => e.cancel(a)));
            return Promise.all(i).then(Fo).catch(Fo)
        }
        invalidateQueries(e, t, n) {
            const [r, a] = Bo(e, t, n);
            return hs.batch(() => {
                var e, t;
                if (this.queryCache.findAll(r).forEach(e => {
                        e.invalidate()
                    }), "none" === r.refetchType) return Promise.resolve();
                const n = {
                    ...r,
                    type: null != (e = null != (t = r.refetchType) ? t : r.type) ? e : "active"
                };
                return this.refetchQueries(n, a)
            })
        }
        refetchQueries(e, t, n) {
            const [r, a] = Bo(e, t, n), i = hs.batch(() => this.queryCache.findAll(r).filter(e => !e.isDisabled()).map(e => {
                var t;
                return e.fetch(void 0, {
                    ...a,
                    cancelRefetch: null == (t = null == a ? void 0 : a.cancelRefetch) || t,
                    meta: {
                        refetchPage: r.refetchPage
                    }
                })
            }));
            let o = Promise.all(i).then(Fo);
            return null != a && a.throwOnError || (o = o.catch(Fo)), o
        }
        fetchQuery(e, t, n) {
            const r = Yo(e, t, n),
                a = this.defaultQueryOptions(r);
            void 0 === a.retry && (a.retry = !1);
            const i = this.queryCache.build(this, a);
            return i.isStaleByTime(a.staleTime) ? i.fetch(a) : Promise.resolve(i.state.data)
        }
        prefetchQuery(e, t, n) {
            return this.fetchQuery(e, t, n).then(Fo).catch(Fo)
        }
        fetchInfiniteQuery(e, t, n) {
            const r = Yo(e, t, n);
            return r.behavior = _s(), this.fetchQuery(r)
        }
        prefetchInfiniteQuery(e, t, n) {
            return this.fetchInfiniteQuery(e, t, n).then(Fo).catch(Fo)
        }
        resumePausedMutations() {
            return this.mutationCache.resumePausedMutations()
        }
        getQueryCache() {
            return this.queryCache
        }
        getMutationCache() {
            return this.mutationCache
        }
        getLogger() {
            return this.logger
        }
        getDefaultOptions() {
            return this.defaultOptions
        }
        setDefaultOptions(e) {
            this.defaultOptions = e
        }
        setQueryDefaults(e, t) {
            const n = this.queryDefaults.find(t => Zo(e) === Zo(t.queryKey));
            n ? n.defaultOptions = t : this.queryDefaults.push({
                queryKey: e,
                defaultOptions: t
            })
        }
        getQueryDefaults(e) {
            if (!e) return;
            const t = this.queryDefaults.find(t => qo(e, t.queryKey));
            return null == t ? void 0 : t.defaultOptions
        }
        setMutationDefaults(e, t) {
            const n = this.mutationDefaults.find(t => Zo(e) === Zo(t.mutationKey));
            n ? n.defaultOptions = t : this.mutationDefaults.push({
                mutationKey: e,
                defaultOptions: t
            })
        }
        getMutationDefaults(e) {
            if (!e) return;
            const t = this.mutationDefaults.find(t => qo(e, t.mutationKey));
            return null == t ? void 0 : t.defaultOptions
        }
        defaultQueryOptions(e) {
            if (null != e && e._defaulted) return e;
            const t = {
                ...this.defaultOptions.queries,
                ...this.getQueryDefaults(null == e ? void 0 : e.queryKey),
                ...e,
                _defaulted: !0
            };
            return !t.queryHash && t.queryKey && (t.queryHash = Wo(t.queryKey, t)), void 0 === t.refetchOnReconnect && (t.refetchOnReconnect = "always" !== t.networkMode), void 0 === t.useErrorBoundary && (t.useErrorBoundary = !!t.suspense), t
        }
        defaultMutationOptions(e) {
            return null != e && e._defaulted ? e : {
                ...this.defaultOptions.mutations,
                ...this.getMutationDefaults(null == e ? void 0 : e.mutationKey),
                ...e,
                _defaulted: !0
            }
        }
        clear() {
            this.queryCache.clear(), this.mutationCache.clear()
        }
    }({
        defaultOptions: {
            queries: {
                refetchOnWindowFocus: !1,
                refetchOnMount: !0,
                retry: !1,
                staleTime: 0,
                cacheTime: 18e5,
                useErrorBoundary: e => (e instanceof qn && Lo(e, {
                    setError: e
                }, !0), !1)
            },
            mutations: {
                retry: 1,
                useErrorBoundary: !1
            }
        }
    });
$n.use(er).use(PP).use(dI).init({
    supportedLngs: ["en", "ar"],
    fallbackLng: "ar",
    detection: {
        order: ["cookie", "htmlTag", "localStorage"],
        caches: ["cookie", "localStorage"]
    },
    backend: {
        loadPath: "/assets/locales/{{lng}}/translation.json",
        requestOptions: {
            cache: "no-store"
        }
    },
    cache: {
        enabled: !1
    },
    load: "languageOnly",
    ns: ["translation"],
    defaultNS: "translation",
    preload: ["en", "ar"]
});
const Yae = "hoa" === Fae ? Hae : Fae,
    Bae = (zae = [
        [Dt.StrictMode],
        [({
            children: t,
            subdomain: n
        }) => {
            const r = "atar",
                a = n || r,
                i = qc[a] ? a : r,
                [o, s] = Dt.useState(i);
            return e.jsx($c.Provider, {
                value: {
                    CurrentBrand: o,
                    setCurrentBrand: s
                },
                children: t
            })
        }, {
            subdomain: Yae
        }],
        [Kt],
        [Nae, {
            appId: "zl1s3n8z"
        }],
        [({
            client: e,
            children: t,
            context: n,
            contextSharing: r = !1
        }) => {
            Dt.useEffect(() => (e.mount(), () => {
                e.unmount()
            }), [e]);
            const a = Rs(n, r);
            return Dt.createElement(Ns.Provider, {
                value: !n && r
            }, Dt.createElement(a.Provider, {
                value: e
            }, t))
        }, {
            client: Rae
        }],
        [({
            children: t
        }) => {
            const [n, r] = _i("user", null), [a, i] = _i("plan", null), [o, s] = _i("planFeatures", null), [l, d] = _i("loggedIn", !1), [c, u] = _i("token", ""), [p, h] = Dt.useState(!1), [m, f] = Dt.useState(!1), {
                CurrentBrand: g
            } = Gc(), [y, v] = Dt.useState(JSON.parse(localStorage.getItem("ejar"))), _ = Ft(), x = Ht(), {
                hardShutdown: b,
                update: w
            } = nc(), C = Ys(), M = n?.subscription?.plan_id === li.Enterprise;
            Dt.useEffect(() => {
                Co(_), n && n?.permissions && ri(n?.permissions)
            }, [n]), Dt.useEffect(() => {
                l && !n && (d(!1), _("/login"))
            }, [l, n]);
            const S = async (e = c, t = "dashboard", n = !1) => {
                try {
                    u(e);
                    const a = await mo(e);
                    if (a) {
                        const e = qd.HmacSHA256(a.tenant_id + a.id, "Ahksk0I19WQlyeCL5Mhr0eG9-O71mmQBygZbzpqv"),
                            o = qd.enc.Hex.stringify(e);
                        d(!0), r(a), i(a?.subscription?.plan_id), s(di(a?.subscription?.plan_id)), _(t, {
                            state: {
                                isOnboarding: n
                            },
                            replace: !0
                        }), ri(a.permissions), w({
                            name: a.name,
                            email: a.email,
                            phone: a.phone_number,
                            userId: a.tenant_id + a.id,
                            userHash: o
                        }), ro.identify(String(a.id), `${String(a.id)} - ${a.name} - ${a.phone_number} - ${a.tenant_id}`, void 0, `${a.name} - ${a.id} - ${a.phone_number} - ${a.tenant_id}`), ro.setTag("role", a.role), ro.setTag("plan_id", a.subscription?.plan_id), ro.setTag("plan_name", a.subscription?.plan_name), ro.setTag("subdomain", g)
                    }
                } catch (a) {
                    d(!1), r(null), _("/login", {
                        replace: !0
                    })
                }
            };
            return Dt.useEffect(() => {
                if (m) return;
                const e = (e => {
                        if ("string" != typeof e) return !1;
                        const t = e.trim();
                        return "" !== t && "null" !== t.toLowerCase() && "undefined" !== t.toLowerCase()
                    })(c),
                    t = (e => {
                        if (!e || "object" != typeof e) return !1;
                        const t = e;
                        return Boolean(t.id && t.tenant_id)
                    })(n);
                e && !t && (f(!0), h(!0), S(c, x?.pathname || "dashboard").finally(() => h(!1)))
            }, [c, n, m, x?.pathname]), e.jsx(Kc.Provider, {
                value: {
                    setUser: r,
                    user: n,
                    isHydratingUser: p,
                    loggedIn: l,
                    fetchProfile: S,
                    refetchProfileInfo: async e => {
                        try {
                            const t = so(),
                                n = await mo(t);
                            if (n) {
                                const t = qd.HmacSHA256(n.tenant_id + n.id, "Ahksk0I19WQlyeCL5Mhr0eG9-O71mmQBygZbzpqv"),
                                    a = qd.enc.Hex.stringify(t);
                                r(n), ri(n.permissions), w({
                                    name: n.name,
                                    email: n.email,
                                    phone: n.phone_number,
                                    userId: n.tenant_id + n.id,
                                    userHash: a
                                }), ro.identify(String(n.id), void 0, void 0, n.name), ro.setTag("email", n.email), ro.setTag("role", n.role), ro.setTag("tenant_id", n.tenant_id), e && (i(n?.subscription?.plan_id), s(di(n?.subscription?.plan_id)))
                            }
                        } catch (t) {
                            r(null)
                        }
                    },
                    logOut: async () => {
                        C.clear(), (async () => {
                            await co("/tenancy/logout")
                        })(), localStorage.removeItem("loggedIn"), localStorage.removeItem("user"), localStorage.removeItem(fi), d(!1), u(""), localStorage.removeItem("X-Tenant"), localStorage.removeItem("token"), localStorage.removeItem("plan"), localStorage.removeItem("planFeatures"), r(null), b(), window.location.replace("/login")
                    },
                    token: c,
                    setToken: u,
                    plan: a,
                    planFeatures: o,
                    ejar: y,
                    setEjar: v,
                    isEnterprise: M
                },
                children: t
            })
        }],
        [({
            children: t
        }) => e.jsx(ai.Provider, {
            value: ni,
            children: t
        })],
        [({
            children: t
        }) => {
            const n = eu.get("i18next") || "en",
                [r, a] = Dt.useState(Jc.find(e => e.code === n));
            return e.jsx(tu.Provider, {
                value: {
                    currentLanguage: r,
                    setCurrentLanguage: a
                },
                children: t
            })
        }],
        [({
            children: t
        }) => {
            const {
                currentLanguage: a
            } = nu(), [i, o] = Dt.useState("dark"), {
                loggedIn: s,
                user: l,
                isHydratingUser: d
            } = Qc(), {
                CurrentBrand: c
            } = Gc(), {
                boot: u
            } = nc();
            ! function() {
                const e = Ht();
                Dt.useEffect(() => {
                    const t = e.pathname + e.search;
                    sessionStorage.setItem(`breadcrumb:${e.pathname}`, t)
                }, [e.pathname, e.search])
            }(), Dt.useEffect(() => {
                XO(c)
            }, []);
            const p = a.dir;
            if (s && l) {
                const e = qd.HmacSHA256(l.tenant_id + l.id, "Ahksk0I19WQlyeCL5Mhr0eG9-O71mmQBygZbzpqv"),
                    t = qd.enc.Hex.stringify(e);
                u({
                    name: l?.name,
                    email: l?.email,
                    phone: l?.phone_number,
                    userId: l?.tenant_id + l?.id,
                    userHash: t,
                    customAttributes: {
                        alignment: "right",
                        horizontal_padding: 10,
                        vertical_padding: 20
                    }
                })
            } else u({
                customAttributes: {
                    alignment: "right",
                    horizontal_padding: 10,
                    vertical_padding: 20
                }
            });
            Dt.useEffect(() => {
                document.body.dir = p, document.documentElement.lang = a.code
            }, [p]);
            const h = Dt.useMemo(() => ((e, t) => n(r({
                    direction: e,
                    palette: {
                        primary: {
                            ...qc[t].primaryPalette
                        }
                    },
                    spacing: (e = 1) => 2 * e + "px"
                })))(p, c), [p, c, i]),
                m = Dt.useMemo(() => Oe({
                    key: "rtl" === h.direction ? "cssrtl" : "cssltr",
                    prepend: !0,
                    stylisPlugins: "rtl" === h.direction && [Ku]
                }), [h.direction]),
                {
                    i18n: {
                        language: f
                    }
                } = Gn(),
                g = t => e.jsx(Pe, {
                    value: m,
                    children: t.children
                }),
                y = qc[c]["rtl" === p ? "arabicFontFamily" : "englishFontFamily"];
            return e.jsx(mP.Provider, {
                value: null,
                children: e.jsx(g, {
                    children: e.jsx(PD, {
                        config: {
                            dir: p,
                            fontFamily: y,
                            mode: "light",
                            lang: f,
                            ...h
                        },
                        children: e.jsx("main", {
                            dir: a.dir,
                            children: d ? e.jsx(hP, {}) : t
                        })
                    })
                })
            })
        }]
    ], zae.reduce((t, [n, r = {}]) => ({
        children: a
    }) => e.jsx(t, {
        children: e.jsx(n, {
            ...r,
            children: a
        })
    }), ({
        children: t
    }) => e.jsx(e.Fragment, {
        children: t
    })));
var zae;

function Uae() {
    var e;
    return e = "h16tldnaou", Dt.useEffect(() => {
        window.clarity || ro.init(e)
    }, [e]), null
}
xI.createRoot(document.getElementById("root")).render(e.jsxs(Bae, {
    children: [e.jsx(je, {}), e.jsx(Uae, {}), e.jsx(Aae, {}), e.jsx(bI, {
        routes: Vae
    }), e.jsx(to, {
        hideProgressBar: !0,
        autoClose: 3e3,
        limit: 5
    }), !1, e.jsx(Pae, {})]
}));
export {
    $I as $, gW as A, cP as B, sP as C, t5 as D, Gee as E, t9 as F, lo as G, uo as H, lP as I, ap as J, hp as K, hP as L, X6 as M, iJ as N, yJ as O, x0 as P, BI as Q, vJ as R, uP as S, rP as T, tF as U, nH as V, po as W, ute as X, Ys as Y, IQ as Z, oi as _, bf as a, wH as a$, qI as a0, r$ as a1, mZ as a2, Gc as a3, qc as a4, Kte as a5, mp as a6, wp as a7, th as a8, jU as a9, P7 as aA, sp as aB, J0 as aC, AH as aD, PH as aE, OH as aF, X0 as aG, IH as aH, SN as aI, e2 as aJ, FH as aK, x1 as aL, WX as aM, _0 as aN, OF as aO, oq as aP, j$ as aQ, uU as aR, jF as aS, J3 as aT, kF as aU, Np as aV, zp as aW, kte as aX, ph as aY, yh as aZ, c8 as a_, AU as aa, j2 as ab, kI as ac, d5 as ad, qX as ae, o1 as af, h3 as ag, RF as ah, HF as ai, p3 as aj, KF as ak, RH as al, gee as am, o7 as an, E5 as ao, RQ as ap, HQ as aq, sJ as ar, oJ as as, GF as at, bh as au, P9 as av, x5 as aw, h5 as ax, Op as ay, $7 as az, fo as b, H9 as b$, zH as b0, km as b1, d1 as b2, q2 as b3, Vp as b4, Q$ as b5, iH as b6, F$ as b7, MH as b8, ho as b9, qJ as bA, i5 as bB, ZJ as bC, uH as bD, PN as bE, Pp as bF, PF as bG, JU as bH, VH as bI, cF as bJ, XI as bK, N9 as bL, wm as bM, A9 as bN, Sf as bO, Cp as bP, Cf as bQ, Ij as bR, eg as bS, nne as bT, tne as bU, Xte as bV, Ch as bW, JI as bX, K3 as bY, Jte as bZ, Qte as b_, E0 as ba, O0 as bb, Fj as bc, Fw as bd, lE as be, jj as bf, GT as bg, no as bh, hZ as bi, DU as bj, EU as bk, VU as bl, o3 as bm, NH as bn, NF as bo, r3 as bp, YF as bq, l3 as br, s3 as bs, a3 as bt, Lm as bu, hq as bv, t$ as bw, gq as bx, TF as by, Jq as bz, vo as c, N7 as c$, W9 as c0, qp as c1, Up as c2, ii as c3, pU as c4, J$ as c5, oH as c6, I2 as c7, Uee as c8, v$ as c9, TU as cA, gF as cB, m$ as cC, c6 as cD, p4 as cE, PQ as cF, j5 as cG, v2 as cH, zQ as cI, UQ as cJ, FQ as cK, Ite as cL, wF as cM, bF as cN, E4 as cO, dp as cP, lp as cQ, yE as cR, D2 as cS, VF as cT, DF as cU, jf as cV, hE as cW, ZF as cX, o6 as cY, JF as cZ, gN as c_, _$ as ca, yF as cb, P$ as cc, jp as cd, Ol as ce, F5 as cf, mo as cg, dZ as ch, xo as ci, u4 as cj, BF as ck, i3 as cl, $F as cm, _F as cn, JZ as co, pF as cp, vi as cq, xH as cr, hh as cs, xp as ct, ni as cu, rh as cv, bH as cw, ore as cx, Ere as cy, lh as cz, o$ as d, nG as d$, g8 as d0, y8 as d1, L8 as d2, P8 as d3, k8 as d4, yp as d5, Y3 as d6, X3 as d7, uF as d8, SD as d9, d6 as dA, e8 as dB, j8 as dC, Qp as dD, b9 as dE, KN as dF, q9 as dG, xF as dH, fH as dI, hH as dJ, eF as dK, mH as dL, pH as dM, Q3 as dN, I8 as dO, pq as dP, g5 as dQ, sH as dR, cH as dS, rG as dT, b$ as dU, x$ as dV, T$ as dW, eG as dX, tG as dY, lH as dZ, X$ as d_, Ao as da, Vo as db, z4 as dc, w6 as dd, Af as de, m6 as df, h6 as dg, P4 as dh, I4 as di, Y4 as dj, EN as dk, oP as dl, fre as dm, IN as dn, wZ as dp, pN as dq, cN as dr, uN as ds, Y7 as dt, MN as du, B7 as dv, V7 as dw, R7 as dx, fF as dy, F7 as dz, a$ as e, u6 as e$, dH as e0, gZ as e1, YQ as e2, qF as e3, LN as e4, WF as e5, UF as e6, bre as e7, Ote as e8, G3 as e9, yH as eA, vH as eB, Q8 as eC, J8 as eD, _H as eE, Bf as eF, O4 as eG, m8 as eH, G5 as eI, ON as eJ, l7 as eK, W8 as eL, q8 as eM, ui as eN, Z4 as eO, p6 as eP, G4 as eQ, m7 as eR, $4 as eS, C6 as eT, W4 as eU, pi as eV, f6 as eW, hi as eX, Sne as eY, E$ as eZ, D$ as e_, QW as ea, KH as eb, jN as ec, i7 as ed, a7 as ee, m2 as ef, lne as eg, Kp as eh, z8 as ei, jre as ej, GN as ek, kN as el, e3 as em, I5 as en, L7 as eo, EF as ep, yne as eq, gne as er, fne as es, rF as et, jne as eu, Ene as ev, Dne as ew, gH as ex, _ne as ey, Tne as ez, tl as f, IF as f$, hU as f0, A$ as f1, V$ as f2, U4 as f3, Y8 as f4, i8 as f5, mN as f6, x6 as f7, _6 as f8, v6 as f9, R$ as fA, N$ as fB, H$ as fC, I$ as fD, CF as fE, Y$ as fF, B$ as fG, l8 as fH, d8 as fI, dc as fJ, Ec as fK, xc as fL, cc as fM, _c as fN, bc as fO, pc as fP, hc as fQ, uc as fR, gc as fS, yc as fT, SZ as fU, rr as fV, p2 as fW, r2 as fX, QI as fY, fU as fZ, gU as f_, N4 as fa, H4 as fb, F4 as fc, S8 as fd, M8 as fe, B8 as ff, a6 as fg, b6 as fh, Mc as fi, hJ as fj, KI as fk, h2 as fl, u2 as fm, hF as fn, aF as fo, iF as fp, $N as fq, $Q as fr, JQ as fs, s8 as ft, o8 as fu, n8 as fv, r8 as fw, t8 as fx, a8 as fy, _8 as fz, M5 as g, U$ as g$, FF as g0, HH as g1, SU as g2, LU as g3, m3 as g4, ro as g5, W3 as g6, Z3 as g7, DI as g8, kJ as g9, Gw as gA, ZL as gB, uj as gC, NT as gD, bS as gE, VM as gF, Hw as gG, xC as gH, QM as gI, XM as gJ, YM as gK, BC as gL, pC as gM, UM as gN, Zf as gO, Kw as gP, s6 as gQ, l6 as gR, S$ as gS, L$ as gT, K8 as gU, K$ as gV, w$ as gW, v7 as gX, _7 as gY, WN as gZ, z$ as g_, J6 as ga, fN as gb, bN as gc, DN as gd, VN as ge, yN as gf, hN as gg, AN as gh, Pte as gi, CN as gj, wN as gk, TN as gl, xN as gm, vN as gn, VT as go, AT as gp, OT as gq, LT as gr, Ww as gs, Bw as gt, Tj as gu, hj as gv, lj as gw, dj as gx, RT as gy, LS as gz, Lo as h, Z8 as h0, C$ as h1, TJ as h2, k$ as h3, vF as h4, M$ as h5, kU as h6, F3 as h7, mU as h8, qN as h9, oc as hA, pee as hB, zF as hC, QZ as hD, up as hE, QF as hF, QN as ha, OU as hb, M3 as hc, v3 as hd, oF as he, HN as hf, RN as hg, BN as hh, Sre as hi, Lre as hj, cre as hk, Aj as hl, c3 as hm, d3 as hn, y7 as ho, IU as hp, PU as hq, XF as hr, Y2 as hs, _N as ht, wI as hu, V6 as hv, lF as hw, dF as hx, u8 as hy, UI as hz, dP as i, tR as j, xW as k, v1 as l, a1 as m, nl as n, L1 as o, $W as p, tH as q, Mm as r, fq as s, AQ as t, Qc as u, yo as v, bo as w, co as x, Zi as y, pP as z
};