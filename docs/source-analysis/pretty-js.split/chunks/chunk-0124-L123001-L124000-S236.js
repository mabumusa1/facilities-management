        isLoading: l
    } = tl({
        queryKey: [YH, t, e],
        queryFn: () => Z9({
            search: t,
            is_paginate: 0
        })
    });
    return {
        sendContract: i,
        isLoading: o,
        users: s,
        isUsersLoading: l,
        setSearch: n,
        search: t
    }
}
const Cre = () => {
        const [t, n] = Dt.useState(!1), [r, a] = Dt.useState(null), {
            t: i
        } = Gn(), o = Lm(), {
            control: s
        } = o, {
            fields: l,
            append: d,
            remove: c
        } = xf({
            name: "signatures",
            control: s
        }), {
            users: u,
            isUsersLoading: p,
            setSearch: h,
            search: m
        } = wre(), f = e => {
            c(e)
        };
        return e.jsxs(ore, {
            title: i("addSignature"),
            children: [e.jsx(hp, {
                variant: "body",
                sx: {
                    width: "fit-content",
                    backgroundColor: "#FCEDC7",
                    p: 6,
                    px: 8,
                    borderRadius: "8px",
                    mt: "16px"
                },
                children: i("addSignatureNote")
            }), e.jsxs(ap, {
                sx: {
                    ".MuiTypography-caption": {
                        color: "text.secondary",
                        fontSize: 12
                    },
                    mt: 20
                },
                children: [e.jsxs(ap, {
                    column: !0,
                    gap: 12,
                    children: [e.jsx(hp, {
                        variant: "body",
                        bold: !0,
                        children: i("firstSignatureSeller")
                    }), e.jsxs(ap, {
                        children: [e.jsxs(ap, {
                            column: !0,
                            children: [e.jsxs(hp, {
                                variant: "smallText",
                                light: !0,
                                gray: !0,
                                children: [i("selectFirstSignature"), " *"]
                            }), e.jsx(wp, {
                                onClick: () => n(!0),
                                sx: {
                                    width: "355px",
                                    height: "48px",
                                    borderRadius: "8px",
                                    color: r ? "text.primary" : "text.disabled",
                                    border: "1px solid #E3E3E3",
                                    justifyContent: "space-between"
                                },
                                endIcon: e.jsx(Bf, {
                                    sx: {
                                        color: "text.primary"
                                    }
                                }),
                                children: r?.name || i("selectUser")
                            })]
                        }), !r && o.formState.isSubmitted && o.formState.submitCount > 0 && e.jsx(N, {
                            style: {
                                color: "#d32f2f",
                                fontSize: "14px"
                            },
                            children: i("selectFirstSignatureError")
                        })]
                    }), e.jsx(bre, {
                        title: i("firstSignatureSeller"),
                        isOpen: t,
                        onClose: () => n(!1),
                        isLoading: p,
                        list: u,
                        value: r,
                        onChange: e => {
                            const t = {
                                ...e,
                                user_id: e.id,
                                name: e.name ?? "",
                                nationalId: e.nationalId ?? "",
                                phone_code: e.phone_country_code ?? "SA",
                                phone: e.phone_number?.replace(/(\+966|\+2)/g, "") ?? "",
                                email: e.email ?? ""
                            };
                            Object.entries(t).forEach(([e, t]) => o.setValue(`signatures.0.${e}`, t)), o.trigger("signatures.0"), a(t), l[0] = t
                        },
                        t: i,
                        search: m,
                        handleSearch: e => h(e)
                    }), !!r && e.jsx(Mre, {
                        field: r,
                        form: o,
                        index: 0,
                        handleDeleteSignature: f,
                        bordered: !1,
                        showTitle: !1
                    })]
                }), l?.slice(1)?.map((t, n) => e.jsx(Mre, {
                    field: t,
                    form: o,
                    index: n + 1,
                    handleDeleteSignature: f,
                    titleIndex: n + 1
                }, t.id)), l?.length < (r ? 11 : 10) && e.jsx(ap, {
                    borderTop: "1px solid #E3E3E3",
                    mt: 10,
                    pt: 3,
                    children: e.jsx(wp, {
                        variant: "text",
                        onClick: () => {
                            d({
                                name: "",
                                nationalId: "",
                                phone_code: "SA",
                                phone: "",
                                email: ""
                            })
                        },
                        startIcon: e.jsx(jf, {}),
                        sx: {
                            px: 0
                        },
                        children: i("addMoreSignatures")
                    })
                })]
            })]
        })
    },
    Mre = ({
        field: t,
        form: n,
        index: r,
        titleIndex: a,
        handleDeleteSignature: i,
        bordered: o = !0,
        showTitle: s = !0
    }) => {
        const [l, d] = Dt.useState(!1), {
            t: c
        } = Gn(), u = n.formState.errors, p = Dt.useMemo(() => ["", "secondSignatureBuyer", "thirdSignature", "fourthSignature", "fifthSignature", "sixthSignature", "seventhSignature", "eighthSignature", "ninthSignature", "tenthSignature"], []), h = Dt.useCallback(e => {
            const {
                isTouched: t,
                isDirty: a,
                invalid: i
            } = n.getFieldState(e);
            return !t && !a && !!n.getValues(e) && !i || 1 === r
        }, []);
        return e.jsxs(ap, {
            column: !0,
            gap: 12,
            children: [o && e.jsx(L, {
                sx: {
                    mt: 12
                }
            }), e.jsxs(ap, {
                row: !0,
                xbetween: !0,
                children: [s && e.jsx(hp, {
                    variant: "body",
                    bold: !0,
                    children: c(p[a])
                }), r > 1 && e.jsx(w, {
                    onClick: () => d(!0),
                    sx: {
                        p: 0
                    },
                    children: e.jsx(th, {
                        sx: {
                            color: "#FF4242"
                        }
                    })
                })]
            }), e.jsxs(ap, {
                row: !0,
                xbetween: !0,
                children: [e.jsxs(ap, {
                    sx: {
                        width: "24%"
                    },
                    children: [e.jsx(o$, {
                        name: `signatures.${r}.name`,
                        control: n.control,
                        label: c("userName") + "*",
                        disabled: h(`signatures.${r}.name`)
                    }), u.signatures?.[r]?.name && e.jsx(N, {
                        style: {
                            color: "#d32f2f",
                            fontSize: "14px"
                        },
                        children: u.signatures?.[r]?.name?.message
                    })]
                }), e.jsxs(ap, {
                    sx: {
                        width: "24%",
                        ml: 8
                    },
                    children: [e.jsx(o$, {
                        name: `signatures.${r}.nationalId`,
                        control: n.control,
                        label: c("common.national_id") + "*",
                        disabled: h(`signatures.${r}.nationalId`)
                    }), u.signatures?.[r]?.nationalId && e.jsx(N, {
                        style: {
                            color: "#d32f2f",
                            fontSize: "14px"
                        },
                        children: u.signatures?.[r]?.nationalId?.message
                    })]
                }), e.jsxs(ap, {
                    sx: {
                        width: "44%",
                        "& .MuiInputBase-root": {
                            boxShadow: "none",
                            border: "1px solid #E3E3E3 !important"
                        },
                        "& fieldset": {
                            border: "none !important"
                        },
                        "& .MuiSelect-select": {
                            py: "11.5px"
                        }
                    },
                    children: [e.jsx(x0, {
                        form: {
                            formState: {
                                errors: []
                            }
                        },
                        phoneCountryCodeName: `signatures.${r}.phone_code`,
                        phoneNumberName: `signatures.${r}.phone`,
                        rules: {
                            required: !0
                        },
                        labelText: c("requests.phoneNumber") + "*",
                        columnSizes: {
                            codeField: 5,
                            phoneField: 6
                        },
                        disabled: h(`signatures.${r}.phone`)
                    }), u.signatures?.[r]?.phone && e.jsx(N, {
                        style: {
                            color: "#d32f2f",
                            fontSize: "14px"
                        },
                        children: u.signatures?.[r]?.phone?.message
                    }), u.signatures?.[r]?.phone_code && e.jsx(N, {
                        style: {
                            color: "#d32f2f",
                            fontSize: "14px"
                        },
                        children: u.signatures?.[r]?.phone_code?.message
                    })]
                }), e.jsxs(ap, {
                    sx: {
                        width: "24%"
                    },
                    children: [e.jsx(o$, {
                        name: `signatures.${r}.email`,
                        control: n.control,
                        label: c("common.email") + "*",
                        disabled: h(`signatures.${r}.email`)
                    }), u.signatures?.[r]?.email && e.jsx(N, {
                        style: {
                            color: "#d32f2f",
                            fontSize: "14px"
                        },
                        children: u.signatures?.[r]?.email?.message
                    })]
                })]
            }, t?.id), e.jsx(lh, {
                closeBtnText: c("no"),
                isOpen: l,
                content: {
                    body: c("deleteSignatureBody"),
                    title: c("deleteSignatureTitle")
                },
                onDialogClose: () => {
                    d(!1)
                },
                primaryButton: {
                    handleClick: () => {
                        i(r), d(!1)
                    },
                    title: c("yes")
                },
                variant: "warning"
            })]
        })
    },
    Sre = e => v1().shape({
        receipts: x1().of(v1().shape({
            id: WX().required(),
            url: a1().required()
        })).min(1, e("invoiceRequired"))
    }),
    Lre = e => v1().shape({
        booking_deposit: a1().required(e("bookingDepositRequired")).test("is-number", e("bookingDepositTwoDecimalPlaces"), e => {
            const t = parseFloat(e);
            return !isNaN(t)
        }).test("positive-number", e("bookingDepositCannotBeNegative"), e => {
            const t = parseFloat(e);
            return isNaN(t) || t > 0
        }).test("minimum-value", e("bookingDepositMinimumValue"), e => {
            const t = parseFloat(e);
            return isNaN(t) || t >= 50
        }).matches(/^[+]?[0-9]*\.?[0-9]{0,2}$/, e("bookingDepositTwoDecimalPlaces")),
        payment_method: a1().oneOf(["sadad", "cash"]).required(),
        receipts: x1().of(v1().shape({
            id: WX().required(),
            url: a1().required()
        })).required(),
        phoneNumber: a1().optional().nullable(),
        phoneCode: a1().optional().nullable().default("SA")
    }),
    kre = e => v1().shape({
        user_id: o1().nullable().optional(),
        name: a1().required(e("newPropertyForm.errorName")).matches(/^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDCF\uFDF0-\uFDFF\uFE70-\uFEFF]*$/, e("errorNameFormat")).max(100, e("errorNameLength")),
        email: a1().email(e("leaseForm.emailInvalid")).required(e("requests.emailRequired")),
        nationalId: a1().required(e("nationalIdRequired")).test("national-id", e("nationalIdOnlyPositiveIntegers"), e => /^[1-9]\d*$/.test(e)).test("national-id", e("nationalIdStartsWith1Or2"), e => ["1", "2"].includes(e?.[0])).test("national-id", e("nationalIdLength"), e => 10 === e?.length),
        phone: a1().required(e("contacts.phoneRequired")).test("phone-saudi", e("phoneShouldBeSaudi"), e => /^5|05(0|1|2|4|3|5|6|7|8|9)\d{7}$/.test(e)),
        phone_code: a1().test("phone-code", e("phoneCodeSA"), e => "SA" === e)
    }),
    Tre = e => v1({
        signatures: x1().of(kre(e)),
        contract: o1().required(e("contractDocumentRequired"))
    }),
    jre = (e, t) => v1().shape({
        description: a1().required(e("fieldRequired")).max(50, e("descriptionMaxLength")),
        amount: o1().transform((e, t) => "" === t ? null : e).required(e("discountAmountRequired")).typeError(e("discountValidNumber")).positive(e("discountAmountPositive")).min(1, e("discountMin")).max(t, e("discountExceedsTotal")).test("decimal-places", e("discountAmountDecimal"), e => {
            if (null == e) return !0;
            return (e.toString().split(".")[1] || "").length <= 2
        })
    }),
    Ere = (e, t) => v1().shape({
        amount: o1().required(e("paymentAmountRequired")).typeError(e("paymentAmountMustBeNumber")).positive(e("paymentAmountCannotBeNegative")).min(50, e("paymentAmountMinimumValue")).max(t, e("paymentAmountExceedsMaximum")).test("decimal-places", e("paymentAmountMustBeNumber"), e => {
            if (null == e) return !0;
            return (e.toString().split(".")[1] || "").length <= 2
        })
    });

function Dre() {
    const {
        t: t
    } = Gn(), [n, r] = Dt.useState(!1), {
        sendContract: a,
        isLoading: i
    } = wre(), [o, s] = Dt.useState(!1), {
        state: l
    } = Ht(), d = bf({
        defaultValues: {
            signatures: [{
                name: "",
                nationalId: "",
                phone: "",
                email: "",
                phone_code: "SA"
            }, {
                name: l?.user?.name,
                nationalId: l?.user?.nationalId,
                phone: l?.user?.phoneNumber?.replace(/(\+966|\+2)/g, ""),
                email: l?.user?.email,
                phone_code: l?.user?.phoneCountryCode ?? "SA"
            }]
        },
        resolver: L1(Tre(t)),
        mode: "onChange",
        reValidateMode: "onChange"
    });
    return e.jsxs(ap, {
        children: [e.jsxs(ap, {
            component: "form",
            onSubmit: d.handleSubmit(() => {
                d.getValues("signatures.0.name") && d.getValues("signatures.0.nationalId") && d.getValues("signatures.0.phone") && d.getValues("signatures.0.email") && r(!0)
            }),
            column: !0,
            gap: 8,
            children: [e.jsxs(km, {
                ...d,
                children: [e.jsx(xre, {
                    updateUploadingStatus: s
                }), e.jsx(Cre, {})]
            }), e.jsx(ap, {
                children: e.jsx(wp, {
                    type: "submit",
                    variant: "contained",
                    disabled: o || i,
                    isLoading: i,
                    sx: {
                        width: "255px",
                        height: "52px"
                    },
                    children: t("sendContract")
                })
            })]
        }), e.jsx(lh, {
            closeBtnText: t("no"),
            isOpen: n,
            content: {
                body: t("sendContractWarning"),
                title: ""
            },
            onDialogClose: () => {
                r(!1)
            },
            primaryButton: {
                handleClick: () => {
                    a(d.getValues()), r(!1)
                },
                title: t("yes")
            },
            variant: "info"
        })]
    })
}
const Vre = Dt.lazy(() => SZ(() => rr(() => import("./AddBooking.page-BTj3RilE.js"), __vite__mapDeps([135, 1, 2, 3, 136, 137, 138, 105, 37, 38, 6])))),
    Are = Dt.lazy(() => SZ(() => rr(() => import("./booking.page-DG1FROr_.js"), __vite__mapDeps([139, 1, 2, 3, 137, 138, 123, 140, 118, 6])))),
    Ore = Dt.lazy(() => SZ(() => rr(() => import("./booking-details.page-Cxy5tSlI.js"), __vite__mapDeps([141, 1, 2, 3, 142, 138, 40, 42, 136, 140, 118, 6])))),
    Pre = Dt.lazy(() => SZ(() => rr(() => import("./review-financials.page-DiumtgPh.js"), __vite__mapDeps([143, 1, 2, 3, 142, 138, 136, 6])))),
    Ire = [{
        path: "/dashboard/booking-contracts",
        title: "bookingContracts",
        children: [{
            title: "",
            path: "",
            element: e.jsx(Are, {})
        }, {
            title: "salesSettings",
            path: "sales-details",
            element: e.jsx(k1, {}),
            nav: !1
        }, {
            title: "bookingDetails",
            path: ":id",
            children: [{
                title: "",
                path: "",
                element: e.jsx(Ore, {})
            }, {
                title: "reviewFinancial",
                path: "review",
                element: e.jsx(Pre, {})
            }]
        }, {
            path: "form",
            title: "addBooking",
            element: e.jsx(Vre, {})
        }, {
            title: "contract",
            path: ":id/contract",
            element: e.jsx(Dre, {})
        }]
    }],
    Fre = Dt.lazy(() => SZ(() => rr(() => import("./ContactTypes-D7d2tRfW.js"), __vite__mapDeps([144, 1, 2, 3, 6])))),
    Hre = Dt.lazy(() => SZ(() => rr(() => import("./SelectCommunityBuilding-BX8HA_2m.js"), __vite__mapDeps([64, 1, 2, 3, 65, 66, 67, 6])))),
    Nre = Dt.lazy(() => SZ(() => rr(() => import("./CreateManager-CQJSa-q9.js"), __vite__mapDeps([145, 1, 2, 3, 146, 147, 66, 67, 148, 6])))),
    Rre = Dt.lazy(() => SZ(() => rr(() => import("./CreateProfessional-D7yMlKei.js"), __vite__mapDeps([149, 1, 2, 3, 146, 147, 66, 67, 148, 6])))),
    Yre = Dt.lazy(() => SZ(() => rr(() => import("./ContactDetails-pqXH4Cx8.js"), __vite__mapDeps([150, 1, 2, 3, 41, 144, 6, 66, 67])))),
    Bre = Dt.lazy(() => SZ(() => rr(() => import("./ContactCU-7VjM6W2C.js"), __vite__mapDeps([151, 1, 2, 3, 148, 147, 6])))),
    zre = Dt.lazy(() => SZ(() => rr(() => import("./ContactCU-7VjM6W2C.js"), __vite__mapDeps([151, 1, 2, 3, 148, 147, 6])))),
    Ure = Dt.lazy(() => SZ(() => rr(() => import("./Contacts-UhIasYmO.js"), __vite__mapDeps([152, 1, 2, 3, 80, 6])))),
    Wre = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => eae), void 0))),
    Zre = Dt.lazy(() => SZ(() => rr(() => import("./ContactFamilyMembers-lsRPynzq.js"), __vite__mapDeps([153, 1, 2, 3, 6])))),
    qre = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => o4), void 0))),
    $re = Dt.lazy(() => SZ(() => rr(() => import("./AddTenant-D4SgoBnd.js"), __vite__mapDeps([154, 1, 2, 3, 147, 6])))),
    Gre = Dt.lazy(() => SZ(() => rr(() => import("./TenantRelatedCompanies-C6itP7J1.js"), __vite__mapDeps([155, 1, 2, 3, 6])))),
    Kre = Dt.lazy(() => SZ(() => rr(() => Promise.resolve().then(() => J7), void 0))),
    Qre = [{
        title: "contacts",
        path: "contacts",
        children: [{
            title: "contact-type",
            path: "",
            element: e.jsx(Fre, {})
        }, {
            title: "contact-list",
            path: ":type",
            element: e.jsx(Ure, {})
        }, {
            title: "my-contacts",
            path: "my/:id",
            element: e.jsx(Bre, {})
        }, {
            title: "create-manager",
            path: `${uU.Manager}/form`,
            children: [{
                title: "",
                path: "",
                element: e.jsx(Nre, {})
            }, {
                title: "update-contact-details",
                path: "selectCommunityBuilding",
                element: e.jsx(Hre, {})
            }]
        }, {
            title: "create-professional",
            path: `${uU.ServiceProfessional}/form`,
            children: [{
                title: "",
                path: "",
                element: e.jsx(Rre, {})
            }, {
                title: "update-contact-details",
                path: "selectCommunityBuilding",
                element: e.jsx(Hre, {})
            }]
        }, {
            title: "create-tenant",
            path: `${uU.Tenant}/form`,
            element: e.jsx($re, {})
        }, {
            title: "edit-tenant",
            path: `${uU.Tenant}/:id/form`,
            element: e.jsx($re, {})
        }, {
            title: "contact-form",
            path: ":type/form",
            children: [{
                title: "",
                path: "",
                element: e.jsx(Bre, {})
            }]
        }, {
            title: "contact-details",
            path: ":type/details/:id",
            children: [{
                title: "",
                path: "",
                element: e.jsx(Yre, {})
            }, {
                title: "family-members",
                path: "family-members",
                element: e.jsx(Zre, {})
            }, {
                title: "serviceRequests",
                path: "active-requests",
                element: e.jsx(Zt, {}),
                children: [{
                    title: "serviceRequests",
                    path: "",
                    element: e.jsx(qre, {
                        isHistory: !1
                    })
                }, {
                    title: "history",
                    path: "history",
                    element: e.jsx(qre, {
                        isHistory: !0
                    })
                }]
            }, {
                title: "visitorAccess",
                path: "visitor-access",
                element: e.jsx(Zt, {}),
                children: [{
                    title: "visitorAccess",
                    path: "",
                    element: e.jsx(Kre, {
                        isHistory: !1
                    })
                }, {
                    title: "history",
                    path: "history",
                    element: e.jsx(Kre, {
                        isHistory: !0
                    })
                }]
            }, {
                title: "related-companies",
                path: "related-companies",
                element: e.jsx(Gre, {})
            }, {
                title: "update-contact-details",
                path: "edit",
                element: e.jsx(zre, {})
            }, {
                title: "update-contact-details",
                path: "selectCommunityBuilding",
                element: e.jsx(Hre, {})
            }, {
                title: "edit-manager",
                path: "edit-manager",
                children: [{
                    title: "",
                    path: "",
                    element: e.jsx(Nre, {})
                }, {
                    title: "update-contact-details",
                    path: "selectCommunityBuilding",
                    element: e.jsx(Hre, {})
                }]
            }, {
                title: "edit-professional",
                path: "edit-professional",
                children: [{
                    title: "",
                    path: "",
                    element: e.jsx(Rre, {})
                }, {
                    title: "update-contact-details",
                    path: "selectCommunityBuilding",
                    element: e.jsx(Hre, {})
                }]
            }]
        }, {
            title: "lease-details",
            path: "leases/:id",
            element: e.jsx(Wre, {})
        }]
    }],
    Jre = ({
        data: t,
        renewalStatus: n
    }) => {
        const {
            t: r
        } = Gn(), a = Ft();
        return e.jsxs(sP, {
            justifyContent: "space-between",
            sx: {
                mt: 12
            },
            children: [e.jsx(l, {
                sx: {
                    flex: 1,
                    mr: 4
                },
                variant: "outlined",
                color: "primary",
                onClick: () => {
                    a("/dashboard/move-out-tenants", {
                        state: {
                            user_id: t?.tenant?.id,
                            property_id: t?.unit?.id || t?.id
                        }
                    })
                },
                children: e.jsx(o, {
                    variant: "caption",
                    sx: {
                        py: 4
                    },
                    children: r("editForm.moveOut")
                })
            }), n && e.jsx(l, {
                sx: {
                    flex: 1
                },
                variant: "contained",
                color: "primary",
                onClick: () => {
                    a("/manual-entry/" + t?.id, {
                        state: {
                            renew: !0,
                            lease: t?.lease
                        }
                    })
                },
                children: r("editForm.renewLease")
            })]
        })
    },
    Xre = () => {
        const {
            t: t
        } = Gn();
        let {
            item: n
        } = Ht().state || {};
        const [r, a] = Dt.useState(!1), [i, s] = Dt.useState(0), {
            id: l
        } = qt(), {
            data: d
        } = tl([rH, l], async () => await (async e => await lo(`/api-management/leases/${e}`))(l));
        n = d?.data;
        return n ? e.jsx(e.Fragment, {
            children: e.jsxs(Ae, {
                maxWidth: "xl",
                children: [e.jsx(IQ, {}), e.jsx(o, {
                    variant: "h5",
                    sx: {
                        my: 10
                    },
                    children: t("lease.lease details")
                }), e.jsx(Ne, {
                    sx: {
                        my: 6
                    },
                    children: e.jsxs(et, {
                        children: [e.jsx(o, {
                            variant: "subtitle1",
                            sx: {
                                mb: 6
                            },
                            children: t("contacts.Tenant Details")
                        }), e.jsx(vJ, {
                            name: n?.tenant?.name || "NA",
                            phone: n?.tenant?.full_phone_number || "Not Assigned"
                        })]
                    })
                }), e.jsx(Ne, {
                    sx: {
                        my: 6
                    },
                    children: e.jsxs(et, {
                        children: [e.jsx(sP, {
                            justifyContent: "space-between",
                            alignItems: "start",
                            children: e.jsx(lP, {})
                        }), e.jsx(yJ, {
                            title: t("requests.status"),
                            value: e.jsx(o, {
                                children: tR(n?.end_date).diff(tR(), "days") > 0 ? e.jsx(p4, {
                                    title: t("status.Active"),
                                    color: "#008EA5",
                                    backgroundColor: "rgba(31, 68, 139, 0.08)"
                                }) : e.jsx(p4, {
                                    title: t("status.Expired"),
                                    color: "#FF0000",
                                    backgroundColor: "#FFEBEB"
                                })
                            })
                        }), n?.contract_number ? e.jsx(yJ, {
                            title: t("signUp.contractNo"),
                            value: n?.contract_number
                        }) : null, e.jsx(yJ, {
                            title: t("complaint.unitNumber"),
                            value: n?.name || n?.unit?.name
                        }), e.jsx(yJ, {
                            title: t("contacts.Community Name"),
                            value: n?.community?.name || t("N/A")
                        }), n?.building?.name && e.jsx(yJ, {
                            title: t("contacts.Building Name"),
                            value: n?.building?.name
                        }), e.jsx(yJ, {
                            title: t("lease.startDate"),
                            value: tR(n?.start_date).format("DD-MM-YYYY")
                        }), e.jsx(yJ, {
                            title: t("lease.endDate"),
                            value: tR(n?.end_date).format("DD-MM-YYYY")
                        }), e.jsx(yJ, {
                            title: t("lease.contact no"),
                            value: n?.tenant?.full_phone_number
                        }), tR(n?.end_date).diff(tR(), "days") > 0 && e.jsx(yJ, {
                            title: t("lease.daysRemaining"),
                            value: n?.remain_days || t("N/A")
                        })]
                    })
                }), n?.files && n?.files?.length > 0 && e.jsx(Ne, {
                    sx: {
                        my: 6
                    },
                    children: e.jsxs(et, {
                        children: [e.jsx(o, {
                            variant: "subtitle1",
                            sx: {
                                mb: 6
                            },
                            children: t("signUp.attachments")
                        }), e.jsxs(sP, {
                            children: [n?.files.filter(e => e.url.match(/\.(jpeg|jpg|gif|png)$/)).map((t, n) => e.jsx(BI, {
                                src: t.url || "",
                                onClick: () => (e => {
                                    s(e), a(!0)
                                })(n),
                                alt: "",
                                sx: {
                                    width: 70,
                                    height: 65,
                                    objectFit: "cover",
                                    borderRadius: "5%",
                                    padding: "4px 10px"
                                }
                            })), r && e.jsx(BI, {
                                src: n?.files.filter(e => e.url.match(/\.(jpeg|jpg|gif|png)$/)).map(e => e.url || ""),
                                currentIndex: i,
                                disableScroll: !1,
                                closeOnClickOutside: !0,
                                onClose: () => {
                                    s(0), a(!1)
                                },
                                backgroundStyle: {
                                    background: "rgba(0,0,0,0.5)",
                                    backdropFilter: "blur(5px)"
                                }
                            })]
                        })]
                    })
                }), !!n && e.jsx(oi, {
                    I: qI.Update,
                    this: $I.Leases,
                    children: e.jsx(Jre, {
                        data: n,
                        renewalStatus: n.renewal_status,
                        refetch: () => {}
                    })
                })]
            })
        }) : e.jsx(hP, {})
    },
    eae = Object.freeze(Object.defineProperty({
        __proto__: null,
        default: Xre
    }, Symbol.toStringTag, {
        value: "Module"
    }));

function tae({
    item: t,
    isMobile: n,
    pathname: r,
    search: a,
    selectedTab: i,
    handleClick: s,
    handleNavigate: l,
    getTabColor: d,
    isTabSelected: c,
    checkCollapse: u,
    t: p,
    onClickOverride: h
}) {
    const {
        text: m,
        to: f,
        icon: g,
        links: y = [],
        isLinkActive: v
    } = t, _ = !!y?.length;
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(ye, {
            button: !0,
            onClick: () => {
                h ? h() : _ ? s(m) : f && l(f, m)
            },
            children: [e.jsx(ze, {
                children: g && e.jsx(g, {
                    sx: {
                        width: "25px",
                        height: "25px",
                        color: e => d({
                            to: f,
                            links: y,
                            text: m,
                            theme: e
                        }) || v?.(r) ? e?.palette?.primary.main : "inherit"
                    }
                })
            }), e.jsx(Ue, {
                primary: e.jsx(o, {
                    sx: {
                        fontSize: "14px !important",
                        fontWeight: 700,
                        color: e => d({
                            to: f,
                            links: y,
                            text: m,
                            theme: e
                        }) || v?.(r) ? e?.palette?.primary.main : "inherit"
                    },
                    children: p(m)
                })
            }), _ && (c(f, y, m) || i === m || v?.(r) ? e.jsx($U, {
                color: c(f, y, m) || v?.(r) ? "primary" : "inherit"
            }) : e.jsx(JU, {}))]
        }), _ && e.jsx(We, {
            in: u(m, y) || v?.(r),
            timeout: "auto",
            unmountOnExit: !0,
            children: y?.filter(e => e.enable).map(({
                text: t,
                to: n,
                selectedTab: i,
                isLinkActive: s
            }) => e.jsx(ye, {
                component: "div",
                disablePadding: !0,
                children: e.jsxs(Ze, {
                    onClick: () => {
                        l(n, "", i)
                    },
                    children: [e.jsx(ze, {}), e.jsx(Ue, {
                        primary: e.jsx(o, {
                            sx: {
                                fontSize: "14px !important",
                                fontWeight: 700,
                                color: e => d({
                                    to: n,
                                    links: [],
                                    text: t,
                                    theme: e
                                }) || s?.(r, a) ? e?.palette?.primary.main : "inherit"
                            },
                            children: p(t)
                        })
                    })]
                })
            }, t))
        })]
    })
}

function nae() {
    const {
        t: t,
        i18n: n
    } = Gn(), r = s(), i = ce(r.breakpoints.down("sm")), o = Ht(), l = Ft(), d = ii(), {
        pathname: c,
        search: u
    } = Ht(), {
        show: p
    } = nc(), [h, m] = Dt.useState(""), {
        CurrentBrand: f
    } = Gc(), {
        planFeatures: g,
        logOut: y
    } = Qc(), {
        currentLanguage: v,
        setCurrentLanguage: _
    } = nu(), x = Ys(), {
        links: b
    } = HU(g, d, f), w = e => m(h === e ? "" : e), C = (e, t = "", n = 0) => {
        m(t), l(e, {
            state: {
                selectedTab: n
            }
        })
    }, M = ({
        to: e,
        links: t,
        text: n,
        theme: r
    }) => {
        if (o?.pathname === e || o?.pathname + window.location.search === e) return r?.palette?.primary?.main;
        if (t?.length) {
            if (S(t)) return r?.palette?.primary?.main
        } else if (h === n) return r?.palette?.primary?.main;
        return ""
    }, S = e => e?.some(e => o?.pathname + window.location.search === e?.to), k = (e, t, n) => {
        if (o?.pathname === e || o?.pathname + window.location.search === e) return !0;
        if (t?.length) {
            if (S(t)) return !0
        } else if (h === n) return !0;
        return !1
    }, T = (e, t) => h === e || !!S(t), j = [{
        key: "support",
        text: "sidebar.support",
        icon: BH,
        onClickOverride: p,
        visible: !0
    }, {
        key: "changeLanguage",
        text: "sidebar.changeLanguage",
        icon: nW,
        onClickOverride: () => {
            const e = Jc.find(e => e.code !== v?.code);
            if (!e) return;
            const t = e.code;
            n.changeLanguage(t), x.clear(), document.documentElement.lang = t, bo.defaults.headers.common["X-App-Locale"] = t, bo.defaults.headers["X-App-Locale"] = t, _(e)
        },
        visible: !0
    }, {
        key: "logout",
        text: "drawer.logout",
        icon: oW,
        onClickOverride: y,
        visible: !0
    }], E = ["/dashboard", "/properties-list", "/requests", "/transactions"], D = Dt.useMemo(() => b.filter(e => !E.includes(e.to)), [b]);
    return e.jsx(a, {
        sx: {
            pb: 10
        },