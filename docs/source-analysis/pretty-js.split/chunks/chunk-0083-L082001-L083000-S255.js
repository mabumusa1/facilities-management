        isPending: i
    } = nl({
        mutationFn: GJ,
        onSuccess() {
            Zi.success(t("settingsSaved"))
        }
    }), o = bf({
        mode: "onChange",
        reValidateMode: "onChange",
        resolver: L1(E1(t)),
        defaultValues: n?.data
    });
    Dt.useEffect(() => {
        o.reset(n?.data)
    }, [n]);
    return r ? e.jsx(hP, {}) : e.jsx(Op, {
        maxWidth: "lg",
        mb: 4,
        children: e.jsxs(ap, {
            column: !0,
            gap: 10,
            px: 12,
            children: [e.jsx(hp, {
                variant: "h5",
                mb: 4,
                children: t("marketSettings.salesSettings")
            }), e.jsx(Cp, {
                body: t("marketSettings.salesSettingsNote"),
                sx: {
                    maxWidth: "80%"
                }
            }), e.jsxs(ap, {
                component: "form",
                column: !0,
                gap: 12,
                onSubmit: o.handleSubmit(e => {
                    a(e)
                }),
                children: [e.jsx(T1, {
                    form: o,
                    name: "deposit_time_limit_days",
                    description: t("depositTimeLimitSetting"),
                    title: t("generalSettings"),
                    endText: t("days")
                }), e.jsx(T1, {
                    form: o,
                    name: "cash_contract_signing_days",
                    description: t("contractSigningTimeLimitSetting"),
                    title: t("cashSales"),
                    endText: t("days")
                }), e.jsx(T1, {
                    form: o,
                    name: "bank_contract_signing_days",
                    description: t("contractSigningTimeLimitSetting"),
                    title: t("bankFinancing"),
                    endText: t("days")
                }), e.jsx(wp, {
                    variant: "contained",
                    type: "submit",
                    disabled: !(ni.can(qI.Update, $I.SalesSettings) || ni.can(qI.Create, $I.SalesSettings)),
                    isLoading: i,
                    sx: {
                        width: "255px"
                    },
                    children: t("saveChanges")
                })]
            })]
        })
    })
}
const T1 = ({
        form: t,
        name: n,
        title: r,
        description: a,
        endText: i
    }) => e.jsxs(j1, {
        children: [e.jsx(hp, {
            variant: "h6",
            children: r
        }), e.jsx(hp, {
            variant: "smallText",
            color: "text.secondary",
            children: a
        }), e.jsx(Cf, {
            name: n,
            control: t.control,
            errors: t.formState.errors,
            sx: {
                maxWidth: "47%"
            },
            InputProps: {
                endAdornment: e.jsx(hp, {
                    variant: "smallText",
                    color: "text.secondary",
                    children: i
                })
            }
        })]
    }),
    j1 = ({
        children: t
    }) => e.jsx(ap, {
        sx: {
            border: "1px solid #CACACA",
            p: "24px",
            borderRadius: "16px",
            maxWidth: "80%"
        },
        column: !0,
        gap: 6,
        children: t
    }),
    E1 = e => v1({
        deposit_time_limit_days: o1().transform((e, t) => "" === t ? null : e).required(e("timeLimitRequired")).typeError(e("timeLimitWholeNumber")).integer(e("timeLimitWholeNumber")).min(1, e("timeLimitMin")).max(50, e("timeLimitMax")).default(3),
        cash_contract_signing_days: o1().transform((e, t) => "" === t ? null : e).typeError(e("timeLimitWholeNumber")).required(e("timeLimitRequired")).integer(e("timeLimitWholeNumber")).min(1, e("timeLimitMin")).max(50, e("timeLimitMax")).default(3),
        bank_contract_signing_days: o1().transform((e, t) => "" === t ? null : e).typeError(e("timeLimitWholeNumber")).required(e("timeLimitRequired")).integer(e("timeLimitWholeNumber")).min(1, e("timeLimitMin")).max(50, e("timeLimitMax")).default(3)
    }),
    D1 = Dt.lazy(() => SZ(() => rr(() => import("./index-D1b-l63d.js"), __vite__mapDeps([43, 1, 2, 3, 6])))),
    V1 = Dt.lazy(() => SZ(() => rr(() => import("./NewSettingsOptions-co4SDNZd.js"), __vite__mapDeps([44, 1, 2, 3, 45, 46, 43, 6, 25, 23, 47, 48, 49, 50, 51])))),
    A1 = Dt.lazy(() => SZ(() => rr(() => import("./ServiceList-3E508p41.js"), __vite__mapDeps([52, 1, 2, 3, 6])))),
    O1 = Dt.lazy(() => SZ(() => rr(() => import("./ServiceSubList-CuGxiF_H.js"), __vite__mapDeps([53, 1, 2, 3, 6])))),
    P1 = Dt.lazy(() => SZ(() => rr(() => import("./ServiceCustomization-B7NzYofa.js"), __vite__mapDeps([54, 1, 2, 3, 46, 6])))),
    I1 = Dt.lazy(() => SZ(() => rr(() => import("./SubcategoriesListing-DgS8XlYx.js"), __vite__mapDeps([55, 1, 2, 3, 45, 6])))),
    F1 = Dt.lazy(() => SZ(() => rr(() => import("./ServiceTypes-DwQ8hg5l.js"), __vite__mapDeps([56, 1, 2, 3, 45, 6])))),
    H1 = Dt.lazy(() => SZ(() => rr(() => import("./TypeDetails-DBFc8hBd.js"), __vite__mapDeps([57, 1, 2, 3, 45, 58, 6])))),
    N1 = Dt.lazy(() => SZ(() => rr(() => import("./VisitorRequestCU-CsA0SJ7s.js"), __vite__mapDeps([59, 1, 2, 3, 50, 6])))),
    R1 = Dt.lazy(() => SZ(() => rr(() => import("./ResetAccount-CZhj2akx.js"), __vite__mapDeps([49, 1, 2, 3, 6])))),
    Y1 = Dt.lazy(() => SZ(() => rr(() => import("./AddNewSubcategory-DwUqTb9H.js"), __vite__mapDeps([60, 1, 2, 3, 22, 58, 61, 62, 6, 63])))),
    B1 = Dt.lazy(() => SZ(() => rr(() => import("./SelectCommunityBuilding-BX8HA_2m.js"), __vite__mapDeps([64, 1, 2, 3, 65, 66, 67, 6])))),
    z1 = Dt.lazy(() => SZ(() => rr(() => import("./TypesForm-CUDQCTtK.js"), __vite__mapDeps([68, 1, 2, 3, 22, 62, 58, 61, 6])))),
    U1 = Dt.lazy(() => SZ(() => rr(() => import("./ServiceDetails-2SNN7qCL.js"), __vite__mapDeps([69, 1, 2, 3, 45, 58, 65, 6])))),
    W1 = Dt.lazy(() => SZ(() => rr(() => import("./CreateForm.page-uaiZocRD.js"), __vite__mapDeps([70, 1, 2, 3, 71, 22, 48, 6])))),
    Z1 = Dt.lazy(() => SZ(() => rr(() => import("./FormPreview.page-fVGVNt_v.js"), __vite__mapDeps([72, 1, 2, 3, 71, 48, 6])))),
    q1 = Dt.lazy(() => SZ(() => rr(() => import("./index-DnkYtIY1.js"), __vite__mapDeps([47, 1, 2, 3, 48, 6])))),
    $1 = Dt.lazy(() => SZ(() => rr(() => import("./SelectCommunities.component-C_JQqTQz.js"), __vite__mapDeps([73, 1, 2, 3, 65, 66, 6])))),
    G1 = Dt.lazy(() => SZ(() => rr(() => import("./SelectBuildings.component-DFTUHhVe.js"), __vite__mapDeps([74, 1, 2, 3, 65, 67, 6])))),
    K1 = Dt.lazy(() => SZ(() => rr(() => import("./BankDetails-DZnm77JK.js"), __vite__mapDeps([75, 1, 2, 3, 6])))),
    Q1 = Dt.lazy(() => SZ(() => rr(() => import("./VisitsSettings-ACg5W26v.js"), __vite__mapDeps([76, 1, 2, 3, 75, 6, 60, 22, 58, 61, 62, 63])))),
    J1 = [{
        title: "Settings",
        path: "settings",
        element: e.jsx(Zt, {}),
        children: [{
            title: "Settings",
            path: "",
            element: e.jsx(V1, {}),
            nav: !1
        }, {
            title: "forms",
            path: "forms/",
            element: e.jsx(Zt, {}),
            nav: !1,
            children: [{
                title: "",
                path: "",
                element: e.jsx(q1, {}),
                nav: !1
            }, {
                title: "createForm",
                path: "create",
                element: e.jsx(W1, {}),
                nav: !1
            }, {
                title: "selectCommunity",
                path: "select-community",
                element: e.jsx($1, {}),
                nav: !1
            }, {
                title: "selectBuilding",
                path: "select-building",
                element: e.jsx(G1, {}),
                nav: !1
            }, {
                title: "formPreview",
                path: "preview/:id",
                element: e.jsx(Z1, {}),
                nav: !1
            }]
        }, {
            title: "homeService",
            path: "home-service-settings/:id",
            element: e.jsx(P1, {}),
            nav: !1
        }, {
            title: "homeService",
            path: ":categoryName/:id",
            element: e.jsx(Zt, {}),
            nav: !1,
            children: [{
                title: "",
                path: "",
                element: e.jsx(I1, {}),
                nav: !1
            }, {
                title: "ServiceDetails",
                path: "ServiceDetails/:subCatId",
                element: e.jsx(U1, {}),
                nav: !0
            }, {
                title: "AddNewSubcategory",
                path: "AddNewSubcategory",
                element: e.jsx(Zt, {}),
                nav: !1,
                children: [{
                    title: "",
                    path: "",
                    element: e.jsx(Y1, {}),
                    nav: !1
                }, {
                    title: "Select Available For",
                    path: "selectCommunityBuilding",
                    element: e.jsx(B1, {}),
                    nav: !0
                }]
            }, {
                title: "EditSubcategory",
                path: "EditSubcategory/:subCatId",
                element: e.jsx(Zt, {}),
                nav: !1,
                children: [{
                    title: "",
                    path: "",
                    element: e.jsx(Y1, {}),
                    nav: !1
                }, {
                    title: "Select Available For",
                    path: "selectCommunityBuilding",
                    element: e.jsx(B1, {}),
                    nav: !0
                }]
            }, {
                title: "subcategoryDetails",
                path: ":subCategoryName/:subCatId",
                element: e.jsx(Zt, {}),
                nav: !1,
                children: [{
                    title: "",
                    path: "",
                    element: e.jsx(F1, {}),
                    nav: !1
                }, {
                    title: "typedetails",
                    path: ":typename/:typeId",
                    element: e.jsx(Zt, {}),
                    nav: !1,
                    children: [{
                        title: "",
                        path: "",
                        element: e.jsx(H1, {}),
                        nav: !1
                    }, {
                        title: "EditTypes",
                        path: "editType",
                        element: e.jsx(z1, {}),
                        nav: !1
                    }]
                }, {
                    title: "AddNewType",
                    path: "newType",
                    element: e.jsx(z1, {}),
                    nav: !1
                }]
            }]
        }, {
            title: "neighbourhoodServices",
            path: "neighbourhood-service-settings/:id",
            element: e.jsx(P1, {}),
            nav: !1
        }, {
            title: "ResetAccount",
            path: "resetAccount",
            element: e.jsx(R1, {}),
            nav: !1
        }]
    }, {
        title: "Invoice",
        path: "settings/invoice/",
        element: e.jsx(D1, {}),
        nav: !0
    }, {
        title: "Request-service",
        path: "settings/service-request/",
        element: e.jsx(A1, {}),
        nav: !0
    }, {
        title: "Service-list",
        path: "settings/service-request/:type/:catCode/:catId",
        element: e.jsx(O1, {}),
        nav: !0
    }, {
        title: "Visitor-request ",
        path: "settings/visitor-request",
        element: e.jsx(N1, {}),
        nav: !0
    }, {
        title: "bankDetails",
        path: "settings/bank-details",
        element: e.jsx(K1, {}),
        nav: !1
    }, {
        title: "visitsDetails",
        path: "settings/visits-details",
        element: e.jsx(Q1, {}),
        nav: !1
    }, {
        title: "salesDetails",
        path: "settings/sales-details",
        element: e.jsx(k1, {}),
        nav: !1
    }],
    X1 = Dt.lazy(() => SZ(() => rr(() => import("./transactions.page-BMduPgD9.js"), __vite__mapDeps([77, 1, 2, 3, 78, 18, 19, 79, 80, 6])))),
    e0 = Dt.lazy(() => SZ(() => rr(() => import("./transactions-history.page-BJK7E0pS.js"), __vite__mapDeps([81, 1, 2, 3, 79, 19, 78, 18, 6])))),
    t0 = Dt.lazy(() => SZ(() => rr(() => import("./transaction-details.page-lwVrWTTe.js"), __vite__mapDeps([82, 1, 2, 3, 19, 83, 6, 78, 18])))),
    n0 = Dt.lazy(() => SZ(() => rr(() => import("./record-transaction.page-DuFmX45x.js"), __vite__mapDeps([84, 1, 2, 3, 83, 19, 6])))),
    r0 = Dt.lazy(() => SZ(() => rr(() => import("./overdues.page-BsguDD8c.js"), __vite__mapDeps([17, 1, 2, 3, 18, 19, 6])))),
    a0 = [{
        title: "Transactions",
        path: "transactions",
        element: e.jsx(X1, {}),
        nav: !0
    }, {
        title: "Transaction-navigate",
        path: "transactions/tenant/:assignee_id",
        element: e.jsx(e0, {}),
        nav: !0
    }, {
        title: "Money-in",
        path: "transactions/money-in",
        element: e.jsx(X1, {
            tab: 2
        }),
        nav: !0
    }, {
        title: "Money-out",
        path: "transactions/money-out",
        element: e.jsx(X1, {
            tab: 3
        }),
        nav: !0
    }, {
        title: "Overdues",
        path: "transactions/overdues",
        element: e.jsx(r0, {}),
        nav: !0
    }, {
        title: "View-transaction",
        path: "transactions/:id",
        element: e.jsx(t0, {}),
        nav: !0
    }, {
        title: "record-transaction-in",
        path: "transactions/record-transaction",
        element: e.jsx(n0, {}),
        nav: !0
    }];

function i0({
    children: t
}) {
    const {
        loggedIn: n
    } = Qc(), r = Ht();
    return n ? e.jsx(Gt, {
        to: "/dashboard",
        state: {
            from: r
        },
        replace: !0
    }) : t
}

function o0({
    children: e
}) {
    return e
}
const s0 = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => T5), void 0))),
    l0 = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => l5), void 0))),
    d0 = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => Q6), void 0))),
    c0 = [{
        title: "settings",
        path: "settings",
        element: e.jsx(Zt, {}),
        children: [{
            title: "addNewFacility",
            path: "addNewFacility/:facilityID?",
            element: e.jsx(s0, {})
        }, {
            title: "facilitiesSettings",
            path: "facilities",
            element: e.jsx(d0, {})
        }, {
            title: "facilityDetails",
            path: "facility/:facilityID",
            element: e.jsx(l0, {})
        }]
    }],
    u0 = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => K7), void 0))),
    p0 = Dt.lazy(() => SZ(() => rr(() => import("./VisitorAccessHistory.page-DYmUvRHI.js"), __vite__mapDeps([85, 1, 2, 3, 6])))),
    h0 = Dt.lazy(() => SZ(() => rr(() => import("./VisitorAccessDetails.page-BNrbBi1Y.js"), __vite__mapDeps([86, 1, 2, 3, 6])))),
    m0 = [{
        title: "visitorAccess",
        path: "visitor-access",
        children: [{
            title: "Requests",
            path: "",
            element: e.jsx(u0, {})
        }, {
            title: "History",
            path: "history",
            element: e.jsx(p0, {})
        }, {
            title: "Visitor Details",
            path: "visitor-details/:id",
            element: e.jsx(h0, {})
        }]
    }],
    f0 = "SEND_CODE_BTN",
    g0 = [uU.Admin, uU.AccountAdmin, uU.Manager, uU.ServiceProfessional, pU.HomeRequests, pU.NeighborhoodRequests, pU.Marketing, pU.Accounting, pU.Leasing];

function y0({
    children: e
}) {
    const t = Ft(),
        {
            CurrentBrand: n
        } = Gc(),
        r = qc[n]?.develop ? n : "",
        a = bf({
            defaultValues: {
                phone_number: "",
                phone_country_code: {
                    id: "SA",
                    name: "SA 966"
                },
                business_name: _Z && "makeen" !== n ? "Demo" : qc[n]?.tenantName || r,
                remember: !1
            },
            mode: "onChange"
        }),
        i = Dt.createRef();
    return e({
        onSubmit: a.handleSubmit(async (e, n) => {
            fq(f0, !0);
            try {
                const {
                    data: r
                } = await (async e => {
                    const {
                        business_name: t,
                        phone_number: n,
                        phone_country_code: r,
                        g_token: a
                    } = e;
                    localStorage.setItem("X-Tenant", t), oo = t;
                    const i = await co("/tenancy/send-verification", {
                        phone_number: n,
                        phone_country_code: r,
                        g_token: a
                    });
                    return Mo(t, a), i
                })({
                    phone_number: e.phone_number,
                    phone_country_code: e.phone_country_code,
                    business_name: e.business_name,
                    g_token: n
                });
                if (r)
                    if (g0.includes(r.role)) {
                        if (r.role === uU.ServiceProfessional && null === r.manager_type) return void t("/no-access");
                        t("/verify", {
                            state: {
                                vid: r.vid,
                                ...e
                            }
                        })
                    } else t("/no-access")
            } catch (r) {
                Lo(r), fq(f0, !1)
            }
        }),
        form: a,
        recaptchaRef: i
    })
}
const v0 = ({
    form: t,
    companyId: n,
    disabled: r,
    rules: a = {}
}) => {
    const i = t.formState.errors,
        {
            t: o
        } = Gn();
    return e.jsx(o$, {
        name: n,
        label: o("signIn.companyId"),
        control: t.control,
        errors: i,
        disabled: r,
        rules: a
    })
};

function _0() {
    const {
        data: e
    } = tl(["COUNTRIES"], ho);
    return {
        data: e,
        getCountryNameWithCode: t => e?.message?.find(e => e.Iso2 === t)?.Name ?? ""
    }
}

function x0({
    form: t,
    phoneCountryCodeName: n,
    phoneNumberName: r,
    disabled: a,
    rules: i,
    isObject: o,
    margin: s = "normal",
    labelText: l = "",
    requiredLabel: d = !1,
    labelSize: c = 14,
    columnSizes: u = {
        codeField: 3.5,
        phoneField: 8.5
    },
    isDark: p = !1
}) {
    const h = t.formState.errors,
        {
            t: m
        } = Gn(),
        {
            data: f
        } = _0(),
        g = f?.message?.map(e => ({
            id: e.Iso2,
            name: `(+${e.Dial} )`,
            listName: `(+${e.Dial}) ${e.Name}  `
        }));
    return e.jsxs(e.Fragment, {
        children: [l && e.jsx(rP, {
            s: c,
            color: "textSecondary",
            sx: {
                fontWeight: 400
            },
            children: l
        }), e.jsxs(sP, {
            sx: {
                display: "flex",
                flexWrap: "wrap",
                justifyContent: "center",
                alignItems: "flex-start"
            },
            children: [e.jsx(lP, {
                lg: u.codeField,
                sx: {
                    pr: {
                        xs: 0,
                        lg: 3
                    },
                    justifyContent: "center",
                    "& .MuiSelect-select": {
                        fontSize: "2rem"
                    },
                    "& #phone_country_code": {
                        py: {
                            lg: "11.5px !important",
                            xs: "1.4rem !important"
                        }
                    },
                    "& .MuiInputBase-root": {
                        mr: {
                            xs: "2rem",
                            sm: "0"
                        }
                    }
                },
                children: e.jsx(oq, {
                    margin: s,
                    name: n,
                    label: l ? "" : `${m("signUp.countryCode")}${d?" *":""}`,
                    labelSize: c,
                    control: t.control,
                    errors: h,
                    valueIsObject: o,
                    options: g || [{
                        id: "SA",
                        name: "(+966)"
                    }],
                    disabled: a,
                    rules: i,
                    isPhone: !0,
                    isDark: p
                })
            }), e.jsx(lP, {
                lg: u.phoneField,
                children: e.jsx(o$, {
                    margin: s,
                    name: r,
                    placeholder: m("common.phonePlaceholder"),
                    type: "tel",
                    inputProps: {
                        "aria-label": "phone"
                    },
                    label: l ? "" : `${m("signIn.mobile")}${d?" *":""}`,
                    control: t.control,
                    errors: h,
                    labelSize: c,
                    disabled: a,
                    onKeyPress: e => {
                        e.key.match(/[^0-9]/g) && e.preventDefault()
                    },
                    rules: {
                        ...i,
                        maxLength: 11,
                        minLength: 9,
                        pattern: /^[0-9]*$/
                    },
                    isDark: p
                })
            })]
        })]
    })
}

function b0({
    handleThemeChange: t = () => {},
    showTranslation: n = !0,
    LogoNavigateTo: r = "/",
    isDark: i
}) {
    const {
        CurrentBrand: o
    } = Gc(), {
        pathname: s
    } = Ht();
    return e.jsx(Ke, {
        position: "fixed",
        variant: "none",
        sx: {
            zIndex: "1000",
            background: "#fff0",
            height: "100px",
            justifyContent: "center"
        },
        children: e.jsxs(qe, {
            sx: {
                alignItems: "center",
                display: "flex",
                justifyContent: "center",
                paddingTop: "20px"
            },
            children: [e.jsx(a, {
                component: Wt,
                to: "/" === r ? qc[o]?.LogoNavigateTo : r,
                children: e.jsx(a, {
                    component: "img",
                    sx: {
                        width: "140px",
                        maxHeight: "180px",
                        display: {
                            xs: "none",
                            md: "flex"
                        },
                        mr: "60px",
                        ml: "16px",
                        pt: "26px",
                        pl: "12px"
                    },
                    src: "/" == s ? qc[o]?.logo : qc[o]?.loginLogo || qc[o]?.logoSm,
                    alt: "Navbar image"
                })
            }), e.jsx(a, {
                component: Wt,
                to: r,
                children: e.jsx(a, {
                    onClick: () => {
                        "/" === r && window.location.replace(qc[o]?.LogoNavigateTo)
                    },
                    component: "img",
                    sx: {
                        width: {
                            md: "140px",
                            xs: "60px"
                        },
                        mr: "50px",
                        display: {
                            xs: "flex",
                            md: "none"
                        }
                    },
                    src: "/" == s ? qc[o]?.logo : qc[o]?.logoSm,
                    alt: "Atar logo"
                })
            }), e.jsx(a, {
                sx: {
                    flexGrow: 1,
                    display: "flex"
                }
            }), e.jsx(lP, {
                xs: 11,
                sm: 6,
                textAlign: "right",
                sx: {
                    pr: 4
                },
                children: n && e.jsx(UI, {
                    isDark: i
                })
            })]
        })
    })
}
var w0, C0 = {};

function M0() {
    if (w0) return C0;
    w0 = 1;
    var e = h();
    Object.defineProperty(C0, "__esModule", {
        value: !0
    }), C0.default = void 0;
    var t = e(jp()),
        n = m();
    return C0.default = (0, t.default)((0, n.jsx)("path", {
        d: "m14 7-5 5 5 5z"
    }), "ArrowLeft"), C0
}
const S0 = It(M0());
var L0, k0 = {};

function T0() {
    if (L0) return k0;
    L0 = 1;
    var e = h();
    Object.defineProperty(k0, "__esModule", {
        value: !0
    }), k0.default = void 0;
    var t = e(jp()),
        n = m();
    return k0.default = (0, t.default)((0, n.jsx)("path", {
        d: "m10 17 5-5-5-5z"
    }), "ArrowRight"), k0
}
const j0 = It(T0());

function E0({
    children: t,
    showBackBtn: n = !1,
    handleBack: r,
    LogoNavigateTo: a = "/",
    hideBackground: i = !1
}) {
    const o = Ft(),
        {
            CurrentBrand: s
        } = Gc(),
        {
            t: d,
            i18n: c
        } = Gn(),
        u = /login|verify/.test(window.location.href) && (qc[s]?.backgroundOverlay || qc[s]?.patternImg),
        p = !!qc[s]?.backgroundOverlay,
        h = !!qc[s]?.isDarkOverlay;
    return e.jsx(e.Fragment, {
        children: e.jsxs(sP, {
            flexDirection: "column",
            justifyContent: "space-between",
            sx: {
                position: "relative",
                bgcolor: "background.default",
                minHeight: "100vh",
                width: "100%",
                "&::before": !i && {
                    content: '""',
                    position: "absolute",
                    backgroundRepeat: "no-repeat",
                    backgroundPosition: "center center",
                    backgroundSize: "cover",
                    right: "en" === c.language ? "0" : "",
                    width: {
                        xs: "100%",
                        md: p ? "100%" : 650.7
                    },
                    height: p ? "100%" : 481 * .9,
                    bottom: "0",
                    backgroundImage: e => "dark" === e.palette.mode ? "none" : `url(${u})`
                }
            },
            children: [e.jsx(b0, {
                LogoNavigateTo: a,
                isDark: h
            }), e.jsx(lP, {
                sx: {
                    width: "100%"
                },
                children: n && e.jsx(l, {
                    variant: "contained",
                    sx: {
                        ml: 8,
                        px: "24px",
                        background: "#fff",
                        color: e => e?.palette?.primary?.main,
                        borderRadius: "8px",
                        "&:hover": {
                            color: "#fff"
                        }
                    },
                    startIcon: "en" === c.language ? e.jsx(S0, {
                        sx: {
                            fontSize: "40px !important",
                            ml: "-5px",
                            mr: "-5px"
                        }
                    }) : e.jsx(j0, {}),
                    onClick: () => r ? r() : o(-1),
                    children: d("common.back")
                })
            }), e.jsx(lP, {
                center: !0,
                sx: {
                    width: "100%",
                    zIndex: "2",
                    flexGrow: 1
                },
                children: t
            })]
        })
    })
}

function D0(e, t) {
    return D0 = Object.setPrototypeOf || function(e, t) {
        return e.__proto__ = t, e
    }, D0(e, t)
}
var V0 = /(http|https):\/\/(www)?.+\/recaptcha/,
    A0 = ["sitekey", "theme", "size", "badge", "tabindex", "hl", "isolated"],
    O0 = function(e) {
        var t, n;

        function r() {
            for (var t, n = arguments.length, r = new Array(n), a = 0; a < n; a++) r[a] = arguments[a];
            return (t = e.call.apply(e, [this].concat(r)) || this).container = void 0, t.timer = void 0, t.state = {
                instanceKey: Date.now(),
                ready: !1,
                rendered: !1,
                invisible: "invisible" === t.props.size
            }, t._isAvailable = function() {
                var e;
                return Boolean(null == (e = window.grecaptcha) ? void 0 : e.ready)
            }, t._inject = function() {
                t.props.inject && ! function(e) {
                    return Array.from(document.scripts).reduce(function(t, n) {
                        return t || e.test(n.src)
                    }, !1)
                }(V0) && function(e) {
                    var t = document.createElement("script");
                    t.async = !0, t.defer = !0, t.src = e, document.head && document.head.appendChild(t)
                }("https://recaptcha.net/recaptcha/api.js?render=explicit" + (t.props.hl ? "&hl=" + t.props.hl : ""))
            }, t._prepare = function() {
                var e = t.props,
                    n = e.explicit,
                    r = e.onLoad;
                window.grecaptcha.ready(function() {
                    t.setState({
                        ready: !0
                    }, function() {
                        n || t.renderExplicitly(), r && r()
                    })
                })
            }, t._renderRecaptcha = function(e, t) {
                return window.grecaptcha.render(e, t)
            }, t._resetRecaptcha = function() {
                return window.grecaptcha.reset(t.state.instanceId)
            }, t._executeRecaptcha = function() {
                return window.grecaptcha.execute(t.state.instanceId)
            }, t._getResponseRecaptcha = function() {
                return window.grecaptcha.getResponse(t.state.instanceId)
            }, t._onVerify = function(e) {
                return t.props.onVerify(e)
            }, t._onExpire = function() {
                return t.props.onExpire && t.props.onExpire()
            }, t._onError = function() {
                return t.props.onError && t.props.onError()
            }, t._stopTimer = function() {
                t.timer && clearInterval(t.timer)
            }, t.componentDidMount = function() {
                t._inject(), t._isAvailable() ? t._prepare() : t.timer = window.setInterval(function() {
                    t._isAvailable() && (t._prepare(), t._stopTimer())
                }, 500)
            }, t.componentWillUnmount = function() {
                t._stopTimer()
            }, t.renderExplicitly = function() {
                return new Promise(function(e, n) {
                    if (t.state.rendered) return n(new Error("This recaptcha instance has been already rendered."));
                    if (!t.state.ready || !t.container) return n(new Error("Recaptcha is not ready for rendering yet."));
                    var r = t._renderRecaptcha(t.container, {
                        sitekey: t.props.sitekey,
                        theme: t.props.theme,
                        size: t.props.size,
                        badge: t.state.invisible ? t.props.badge : void 0,
                        tabindex: t.props.tabindex,
                        callback: t._onVerify,
                        "expired-callback": t._onExpire,
                        "error-callback": t._onError,
                        isolated: t.state.invisible ? t.props.isolated : void 0,
                        hl: t.state.invisible ? void 0 : t.props.hl
                    });
                    t.setState({
                        instanceId: r,
                        rendered: !0
                    }, function() {
                        t.props.onRender && t.props.onRender(), e()
                    })
                })
            }, t.reset = function() {
                return new Promise(function(e, n) {
                    if (t.state.rendered) return t._resetRecaptcha(), e();
                    n(new Error("This recaptcha instance did not render yet."))
                })
            }, t.execute = function() {
                return new Promise(function(e, n) {
                    return t.state.invisible ? (t.state.rendered && (t._executeRecaptcha(), e()), n(new Error("This recaptcha instance did not render yet."))) : n(new Error("Manual execution is only available for invisible size."))
                })
            }, t.getResponse = function() {
                return new Promise(function(e, n) {
                    if (t.state.rendered) return e(t._getResponseRecaptcha());
                    n(new Error("This recaptcha instance did not render yet."))
                })
            }, t.render = function() {
                var e = Vt.createElement("div", {
                    key: t.state.instanceKey,
                    id: t.props.id,
                    className: t.props.className,
                    ref: function(e) {
                        return t.container = e
                    }
                });
                return t.props.children ? t.props.children({
                    renderExplicitly: t.renderExplicitly,
                    reset: t.reset,
                    execute: t.execute,
                    getResponse: t.getResponse,
                    recaptchaComponent: e
                }) : e
            }, t
        }
        return n = e, (t = r).prototype = Object.create(n.prototype), t.prototype.constructor = t, D0(t, n), r.getDerivedStateFromProps = function(e, t) {
            var n = "invisible" === e.size;
            return n !== t.invisible ? {
                invisible: n
            } : null
        }, r.prototype.componentDidUpdate = function(e) {
            var t = this;
            A0.reduce(function(n, r) {
                return t.props[r] !== e[r] ? [].concat(n, [r]) : n
            }, []).length > 0 && this.setState({
                instanceKey: Date.now(),
                rendered: !1
            }, function() {
                t.props.explicit || t.renderExplicitly()
            })
        }, r
    }(Dt.Component);
O0.defaultProps = {
    id: "",
    className: "g-recaptcha",
    theme: "light",
    size: "normal",
    badge: "bottomright",
    tabindex: 0,
    explicit: !1,
    inject: !0,
    isolated: !1,
    hl: ""
};
const P0 = ({
        onSubmit: t,
        form: n,
        recaptchaRef: r
    }) => {
        const {
            t: a,
            i18n: i
        } = Gn();
        Ft();
        const {
            CurrentBrand: o
        } = Gc(), l = s();
        ce(l.breakpoints.down("sm")), Dt.useEffect(() => {
            localStorage.removeItem(qO)
        }, []), Dt.useEffect(() => {