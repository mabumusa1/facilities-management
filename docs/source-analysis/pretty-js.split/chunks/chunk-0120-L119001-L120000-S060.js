            }) : e.jsx(hp, {
                s: "16",
                light: !0,
                color: "red",
                children: i("signUp.missingData")
            })
        }) : e.jsx(pP, {
            component: "td",
            scope: "row",
            children: t?.price ? e.jsx(hp, {
                s: "16",
                light: !0,
                currency: !0,
                children: d6(t?.price)
            }) : e.jsx(hp, {
                s: "16",
                light: !0,
                color: "red",
                children: i("signUp.missingData")
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: t?.deposit ? e.jsx(hp, {
                s: "16",
                light: !0,
                currency: !0,
                color: r && t?.isHidden ? "#CACACA" : "black",
                children: d6(t?.deposit)
            }) : e.jsx(hp, {
                s: "16",
                light: !0,
                color: "red",
                children: i("signUp.missingData")
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            children: e.jsx(ve, {
                label: t?.status?.name,
                sx: {
                    bgcolor: Hte[t?.status?.id]?.bg,
                    color: Hte[t?.status?.id]?.txt
                }
            })
        }), r && e.jsx(pP, {
            component: "td",
            scope: "row",
            children: a({
                isHidden: t?.isHidden,
                id: t?.id,
                disabled: o
            })
        }), e.jsx(pP, {
            component: "td",
            scope: "row",
            sx: {
                display: "flex",
                gap: "8px",
                justifyContent: "end"
            },
            children: n?.map(n => "edit" === n?.name && t?.status?.id === t9.BOOKED ? null : e.jsx(dP, {
                ...n,
                disabled: s(n),
                onClick: () => (e => {
                    e?.onClick(t?.id)
                })(n),
                isLoading: "list" === n?.name && n?.isLoading(t?.id),
                children: i(n?.label)
            }))
        })]
    })
}

function Rte({
    isRent: t,
    handleClose: n,
    submit: r,
    isSubmitting: a,
    defaultValues: i
}) {
    const {
        t: o
    } = Gn(), s = bf({
        shouldUnregister: !1,
        reValidateMode: "onChange",
        mode: "onChange",
        resolver: L1(i9(o)),
        defaultValues: {
            area: i?.area,
            price: i?.price || null,
            deposit: i?.deposit || null
        }
    }), l = () => {
        n(), s.reset()
    };
    return e.jsxs(v, {
        onClose: l,
        open: !0,
        fullWidth: !0,
        maxWidth: "xs",
        children: [e.jsx(TJ, {
            title: o("marketplace.editUnitTitle"),
            subtitle: o("marketplace.fillRequired"),
            handleClose: l
        }), e.jsx(_, {
            children: e.jsxs(cP, {
                component: "form",
                onSubmit: s.handleSubmit(r),
                sx: {
                    display: "flex",
                    flexDirection: "column",
                    gap: "1rem"
                },
                children: [e.jsx(o$, {
                    name: "area",
                    label: `${o("unitForm.unitArea")}*`,
                    errors: s.formState.errors,
                    control: s.control
                }), e.jsx(o$, {
                    name: "price",
                    label: `${o(t?"annualRentalPrice":"Price")}*`,
                    errors: s.formState.errors,
                    control: s.control
                }), e.jsx(o$, {
                    name: "deposit",
                    label: o("unitForm.bookingDeposit"),
                    errors: s.formState.errors,
                    control: s.control
                }), e.jsx(dP, {
                    variant: "contained",
                    type: "submit",
                    fullWidth: !0,
                    disabled: a || !s.formState.isValid,
                    isLoading: a,
                    children: o("common.save")
                })]
            })
        })]
    })
}
var Yte = (e => (e.IS_PREVIEW_MODE = "isPreviewMode", e.COMMUNITY = "community", e.PRODUCT_CODE = "productCode", e.PURCHASE_METHOD = "purchaseMethod", e.LICENSE_NUMBER = "licenseNumber", e.LICENSE_ISSUE_DATE = "licenseIssueDate", e.LICENSE_EXPIRY_DATE = "licenseExpiryDate", e.PAYMENTS = "payments", e.COMMUNITY_ID = "communityId", e.COMMUNITY_NAME = "communityName", e))(Yte || {});
const Bte = async ({
    search: e,
    page: t,
    subCategoryId: n
}) => {
    try {
        const e = await lo("/api-management/rf/communities/edaat/product-codes");
        return zte(e)
    } catch (r) {
        throw r
    }
}, zte = e => e?.data?.map(e => ({
    id: e?.id,
    name: e?.code
})) ?? [];

function Ute(e) {
    const t = function({
            queries: e,
            context: t
        }) {
            const n = Ys({
                    context: t
                }),
                r = zs(),
                a = Ws(),
                i = Dt.useMemo(() => e.map(e => {
                    const t = n.defaultQueryOptions(e);
                    return t._optimisticResults = r ? "isRestoring" : "optimistic", t
                }), [e, n, r]);
            i.forEach(e => {
                Ks(e), qs(e, a)
            }), $s(a);
            const [o] = Dt.useState(() => new js(n, i)), s = o.getOptimisticResult(i);
            Fs(Dt.useCallback(e => r ? () => {} : o.subscribe(hs.batchCalls(e)), [o, r]), () => o.getCurrentResult(), () => o.getCurrentResult()), Dt.useEffect(() => {
                o.setQueries(i, {
                    listeners: !1
                })
            }, [i, o]);
            const l = s.some((e, t) => Js(i[t], e, r)),
                d = l ? s.flatMap((e, t) => {
                    const n = i[t],
                        s = o.getObservers()[t];
                    if (n && s) {
                        if (Js(n, e, r)) return Xs(n, s, a);
                        Qs(e, r) && Xs(n, s, a)
                    }
                    return []
                }) : [];
            if (d.length > 0) throw Promise.all(d);
            const c = o.getQueries(),
                u = s.find((e, t) => {
                    var n, r;
                    return Gs({
                        result: e,
                        errorResetBoundary: a,
                        useErrorBoundary: null != (n = null == (r = i[t]) ? void 0 : r.useErrorBoundary) && n,
                        query: c[t]
                    })
                });
            if (null != u && u.error) throw u.error;
            return s
        }({
            queries: (e?.filter(e => !!e).map(e => +e).filter(e => !Number.isNaN(e)) ?? []).map(e => ({
                queryKey: [nF, "FAILED_PAYMENTS", e],
                queryFn: () => (async e => {
                    try {
                        return await lo(`/api-management/marketplace/admin/communities/resend/payment-schedules/failed/${e}`, {
                            is_paginate: 1
                        })
                    } catch (t) {
                        throw t
                    }
                })(e),
                enabled: !!e
            }))
        }),
        n = t.reduce((e, t) => {
            const n = t.data?.data?.paginator?.total ?? 0;
            return e + (Number.isFinite(n) ? n : 0)
        }, 0),
        r = t.some(e => e.isLoading),
        a = t.some(e => e.isFetching);
    return {
        failedPaymentsCount: n,
        isLoading: r,
        isFetching: a
    }
}
var Wte = (e => (e.MARK_AS_COMPLETE = "markAsComplete", e.SEND_PAYMENT = "sendPayment", e.REMIND_CUSTOMER = "remindCustomer", e))(Wte || {});

function Zte(e) {
    const {
        t: t
    } = Gn(), n = Ft(), r = Ys(), [a, i] = Dt.useState(!1), [o, s] = Dt.useState(!1), [l, d] = Dt.useState("markAsComplete"), [c, u] = Dt.useState([]), {
        data: p,
        isFetching: h,
        refetch: m
    } = tl({
        queryKey: [JI, nF, e],
        queryFn: () => (async e => {
            try {
                return await lo(`/api-management/rf/communities/${e}`)
            } catch (t) {
                throw t
            }
        })(Number(e)),
        enabled: !!e,
        cacheTime: 0,
        refetchOnMount: "always",
        refetchOnWindowFocus: "always",
        refetchOnReconnect: "always",
        refetchInterval: 0,
        refetchIntervalInBackground: !1
    }), f = !!e && h, g = c.filter(e => !1 === e.show_btn_send_payment).map(e => e.id).filter(e => !!e), {
        failedPaymentsCount: y
    } = Ute(g), v = e => ({
        id: e.id,
        paymentValue: e.value_percent,
        paymentTrigger: +e.completion_percent ? "basedOnProjectCompletion" : "afterContractSigning",
        projectCompletion: parseFloat(e.completion_percent),
        status: e.status,
        show_btn_send_payment: e.show_btn_send_payment,
        show_btn_send_reminder: e.show_btn_send_reminder,
        all_unit_payments_count: e.all_unit_payments_count,
        is_sent_count: e.is_sent_count,
        is_success_count: e.is_success_count,
        is_fail_count: e.is_fail_count,
        bulk_payment_status: e.bulk_payment_status
    });
    Dt.useEffect(() => {
        const t = !!e;
        if (C.setValue(Yte.IS_PREVIEW_MODE, t), t && p) {
            const e = p?.data;
            C.setValue(Yte.COMMUNITY, [{
                id: e.id,
                name: e.name
            }]), C.setValue(Yte.PRODUCT_CODE, [{
                id: e.id,
                name: e.product_code
            }]), C.setValue(Yte.LICENSE_NUMBER, e.license_number), C.setValue(Yte.LICENSE_ISSUE_DATE, new Date(e.license_issue_date)), C.setValue(Yte.LICENSE_EXPIRY_DATE, new Date(e.license_expiry_date)), C.setValue(Yte.PURCHASE_METHOD, !0);
            const t = (e.record_payments ?? []).map(v);
            u(t), C.setValue(Yte.PAYMENTS, t)
        }
    }, [e, p]);
    const _ = new Date,
        x = new Date(_.getFullYear() - 30, _.getMonth(), _.getDate()),
        b = new Date(_.getFullYear() + 30, _.getMonth(), _.getDate()),
        w = v1().shape({
            [Yte.IS_PREVIEW_MODE]: qX(),
            [Yte.COMMUNITY]: x1().when(Yte.IS_PREVIEW_MODE, {
                is: !1,
                then: e => e.min(1, t("pleaseSelectACommunity")),
                otherwise: e => e.notRequired()
            }),
            [Yte.PRODUCT_CODE]: x1().min(1, t("pleaseSelectAProductCode")),
            [Yte.PURCHASE_METHOD]: qX().required(),
            [Yte.LICENSE_NUMBER]: a1().required(t("licenseNumberRequired")).max(30, t("licenseNumberMaxLength")).matches(/^[A-Za-z0-9أ-ي\s\-/_.،,]+$/, t("licenseNumberInvalid")),
            [Yte.LICENSE_ISSUE_DATE]: d1().required(t("pleaseSelectLicenseIssueDate")).min(x, t("dateRangeError")).max(b, t("dateRangeError")),
            [Yte.LICENSE_EXPIRY_DATE]: d1().required(t("pleaseSelectLicenseExpiryDate")).min(x, t("dateRangeError")).max(b, t("dateRangeError")).test("is-after-issue-date", t("expiryDateMustBeAfterIssueDate"), function(e) {
                const t = this.parent[Yte.LICENSE_ISSUE_DATE];
                return !e || !t || new Date(e) >= new Date(t)
            }),
            [Yte.PAYMENTS]: x1().of(v1())
        }),
        C = bf({
            defaultValues: {
                [Yte.IS_PREVIEW_MODE]: !1,
                [Yte.COMMUNITY]: [],
                [Yte.PRODUCT_CODE]: [],
                [Yte.PURCHASE_METHOD]: !0,
                [Yte.LICENSE_NUMBER]: "",
                [Yte.LICENSE_ISSUE_DATE]: null,
                [Yte.LICENSE_EXPIRY_DATE]: null,
                [Yte.PAYMENTS]: []
            },
            resolver: L1(w),
            mode: "onChange"
        }),
        M = nl({
            mutationFn: e => (async e => {
                try {
                    return await co("/api-management/rf/communities/off-plan-sale", e)
                } catch (t) {
                    throw t
                }
            })(e),
            onSuccess: e => {
                Zi.success(t("offPlanSaleCreatedSuccessfully")), r.invalidateQueries({
                    queryKey: [SH]
                }), r.invalidateQueries({
                    queryKey: [nF]
                });
                const a = `/marketplace/listing/manage/${e.data.community_id}?listFor=sale&communityName=${encodeURIComponent(e.data.name)}&isOfPlan=1`;
                n(a)
            },
            onError: e => {}
        }),
        S = nl({
            mutationFn: ({
                communityId: e,
                payload: t
            }) => (async (e, t) => {
                try {
                    return await uo(`/api-management/rf/communities/${e}/off-plan-sale/license`, t)
                } catch (n) {
                    throw n
                }
            })(e, t),
            onSuccess: () => {
                Zi.success(t("unitForm.offPlanSaleUpdatedSuccessfully")), r.invalidateQueries({
                    queryKey: [nF, e]
                }), i(!1)
            },
            onError: e => {}
        }),
        L = nl({
            mutationFn: ({
                communityId: e,
                paymentId: t
            }) => (async (e, t) => {
                try {
                    return await co(`/api-management/rf/communities/${e}/off-plan-sale/payments/${t}/complete`)
                } catch (n) {
                    throw n
                }
            })(e, t),
            onSuccess: () => {
                Zi.success(t("unitForm.paymentMarkedAsComplete")), r.invalidateQueries({
                    queryKey: [nF, e]
                }), m()
            },
            onError: e => {}
        }),
        k = nl({
            mutationFn: e => (async e => {
                try {
                    return await co(`/api-management/marketplace/admin/communities/resend/bulk-payments/${e}`)
                } catch (t) {
                    throw t
                }
            })(e),
            onSuccess: () => {
                Zi.success(t("unitForm.paymentNotificationsSentSuccessfully")), r.invalidateQueries({
                    queryKey: [nF, e]
                }), m()
            },
            onError: e => {}
        }),
        T = nl({
            mutationFn: e => (async e => {
                try {
                    return await co(`/api-management/marketplace/admin/communities/resend/bulk-reminder/${e}`)
                } catch (t) {
                    throw t
                }
            })(e),
            onSuccess: () => {
                Zi.success(t("unitForm.paymentRemindersSentSuccessfully")), r.invalidateQueries({
                    queryKey: [nF, e]
                }), m()
            },
            onError: e => {}
        });
    return {
        form: C,
        onSubmit: async e => {
            try {
                if (e[Yte.IS_PREVIEW_MODE]) {
                    const t = e[Yte.COMMUNITY]?.[0]?.id,
                        n = {
                            license_number: e[Yte.LICENSE_NUMBER],
                            license_issue_date: new Date(e[Yte.LICENSE_ISSUE_DATE]).toISOString().split("T")[0],
                            license_expiry_date: new Date(e[Yte.LICENSE_EXPIRY_DATE]).toISOString().split("T")[0]
                        };
                    await S.mutateAsync({
                        communityId: t,
                        payload: n
                    })
                } else {
                    const t = e[Yte.COMMUNITY]?.[0]?.id,
                        n = e[Yte.PRODUCT_CODE]?.[0]?.name,
                        r = e[Yte.PAYMENTS] || [],
                        a = {
                            community_id: t,
                            product_code: n,
                            license_number: e[Yte.LICENSE_NUMBER],
                            license_issue_date: new Date(e[Yte.LICENSE_ISSUE_DATE]).toISOString().split("T")[0],
                            license_expiry_date: new Date(e[Yte.LICENSE_EXPIRY_DATE]).toISOString().split("T")[0],
                            payments: r.map(e => ({
                                value_percent: e.paymentValue,
                                completion_percent: Number(e.projectCompletion)
                            }))
                        };
                    await M.mutateAsync(a)
                }
            } catch (t) {}
        },
        isLoading: f,
        btnLoading: M.isPending || S.isPending,
        minDate: x,
        maxDate: b,
        isPreviewMode: C.watch(Yte.IS_PREVIEW_MODE),
        isEditEnabled: a,
        toggleEdit: () => {
            if (a && p?.data) {
                const e = p.data;
                C.setValue(Yte.LICENSE_NUMBER, e.license_number), C.setValue(Yte.LICENSE_ISSUE_DATE, new Date(e.license_issue_date)), C.setValue(Yte.LICENSE_EXPIRY_DATE, new Date(e.license_expiry_date))
            }
            i(e => !e)
        },
        handleMarkAsComplete: async (e, t) => {
            if (s(!1), !e) return;
            const n = C.getValues(Yte.COMMUNITY)?.[0]?.id;
            if (n) try {
                await L.mutateAsync({
                    communityId: n,
                    paymentId: Number(e)
                })
            } catch (r) {}
        },
        handleSendPayment: async (e, t) => {
            if (s(!1), e) try {
                await k.mutateAsync(Number(e))
            } catch (n) {}
        },
        handleNotifyCustomers: async (e, t) => {
            if (s(!1), e) try {
                await T.mutateAsync(Number(e))
            } catch (n) {}
        },
        isConfirmOpen: o,
        setIsConfirmOpen: s,
        confirmationType: l,
        setConfirmationType: d,
        payments: c,
        setPayments: u,
        offPlanData: p?.data,
        failedPaymentsCount: y
    }
}
const qte = ({
        children: t,
        sx: n = {}
    }) => e.jsx(sP, {
        row: !0,
        rowSpacing: 8,
        columnSpacing: 12,
        sx: {
            border: "1px solid #E3E3E3",
            borderRadius: "16px",
            padding: "36px 36px 36px 18px",
            background: "white",
            ...n
        },
        children: t
    }),
    $te = ({
        isPreviewMode: t,
        form: n,
        onSubmit: r,
        children: a,
        isLoading: i,
        btnLoading: o
    }) => {
        const {
            t: s
        } = Gn();
        return i ? e.jsx(cP, {
            xcenter: !0,
            ycenter: !0,
            fullHeight: !0,
            children: e.jsx(d, {})
        }) : e.jsx(km, {
            ...n,
            children: e.jsx(cP, {
                ml: 20,
                mt: 20,
                component: "form",
                onSubmit: n.handleSubmit(r),
                children: e.jsxs(qte, {
                    sx: {
                        padding: t ? "16px 36px 36px 18px" : "36px 36px 36px 18px"
                    },
                    children: [!t && e.jsx(lP, {
                        xs: 12,
                        children: e.jsx(cP, {
                            mt: -10,
                            children: e.jsx(IQ, {})
                        })
                    }), a, !t && e.jsx(lP, {
                        xs: 12,
                        children: e.jsxs(dP, {
                            type: "submit",
                            variant: "contained",
                            sx: {
                                width: "255px"
                            },
                            disabled: o,
                            children: [!o && s("listInMarketplace"), o && e.jsx(cP, {
                                center: !0,
                                p: 3,
                                children: e.jsx(d, {
                                    size: 15
                                })
                            })]
                        })
                    })]
                })
            })
        })
    },
    Gte = ({
        title: t,
        statusLabel: n,
        progress: r
    }) => {
        const {
            t: a
        } = Gn();
        return e.jsx(sh, {
            progress: r,
            title: t || a("projectCompletionTitle"),
            statusLabel: `${r}% ${n||a("common.complete")}`
        })
    },
    Kte = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADYAAAAwCAYAAABaHInAAAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAGfSURBVHgB7ZrfTYRAEMaHf++UgB1IArxKCdeBZwl2oBUYKxAr0A7EJxIgwQ6kBB9JIOBswl32UI9jvYcdMr9kczDZPfLldobZL2eARJZlnmVZT3gZg57UhmEkYRjez0005Rvbtl9AX1ECbxiGu6Io4rmJe2FVVbm46BIIgL/a9dycvbCmaVxYESasFBZGDRZGjdUKs0GNGkcqB/Ad6OL7ZQOaoCTMNM00CIIbOTa2Y9oI4xyjBgujhrG7GJP/E+hSR1F0sbvhrUgNFkYNpc4D26d3YarIMbwX1sKD6vqFz4/n7AElYaJ6YkuVyLGxqp4k7Lf1S8jzXHwcFcY5Rg0uHjJ933u4z7dybCweoIrwNdu2/XHscRzn1ff9L1iI6kEzholj/B9RAuFrjvb6AV3X1TA51J4C5xg1WBg1VM2c5A8zR5vzHG9FarAwaqi2VDG2VAddgrC4QSNUWyoPx1YOYK8IOsE5Ro31OsFlWW6wGLhLzJgzkOJ4lgNHnv+I42MSu4JJjiPizHY7fldtYHV7A73/jbMY0fJxjlGDhVHjG9PngVPODIoIAAAAAElFTkSuQmCC";

function Qte({
    item: t
}) {
    return e.jsxs(ap, {
        row: !0,
        gap: "12px",
        children: [e.jsx(ap, {
            sx: {
                width: "40px",
                height: "40px",
                borderRadius: "8px",
                objectFit: t?.logo ? "cover" : "contain"
            },
            component: "img",
            src: t?.logo || Kte,
            alt: "community-avatar"
        }), e.jsxs(ap, {
            column: !0,
            children: [e.jsx(hp, {
                variant: "label",
                bold: !0,
                children: t?.name
            }), e.jsx(hp, {
                variant: "smallText",
                children: `${t?.city}, ${t?.district}`
            })]
        })]
    })
}
const Jte = ({
        title: t,
        description: n
    }) => e.jsxs(ap, {
        column: !0,
        ycenter: !0,
        gap: "2px",
        py: "180px",
        children: [e.jsx(hp, {
            variant: "h4",
            bold: !0,
            s: 24,
            children: t
        }), e.jsx(hp, {
            variant: "body",
            width: 300,
            textAlign: "center",
            children: n
        })]
    }),
    Xte = ({
        isRTL: t = !1
    }) => e.jsx("img", {
        style: {
            objectFit: "contain",
            position: "absolute",
            width: "63px",
            top: "8px",
            left: t ? "12px" : void 0,
            right: t ? void 0 : "12px"
        },
        alt: "ssss",
        src: "data:image/png;base64,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"
    }),
    ene = [...dp.image, ...dp.pdf],
    tne = ({
        form: t,
        name: n
    }) => {
        const {
            t: r,
            i18n: a
        } = Gn();
        a.language;
        const i = t?.formState?.errors;
        return e.jsxs(ap, {
            column: !0,
            gap: "12px",
            mt: "12px",
            children: [e.jsx(SD, {
                label: `${r("bookingForm.uploadDocument")}*`,
                name: n,
                form: t,
                maxFileSize: lp,
                formats: ene,
                filesLimit: 5,
                uploadImgAsync: Vo,
                removeImageAsync: Ao
            }), i && i?.[n] && e.jsx(hp, {
                color: "#f44336",
                variant: "smallText",
                children: `${i?.[n]?.message}`
            })]
        })
    },
    nne = () => e.jsx(ap, {
        sx: {
            width: "100%",
            height: "1px",
            backgroundColor: "#E3E3E3"
        }
    }),
    rne = e => v1().shape({
        paymentValue: o1().transform((e, t) => "" === t ? null : e).required(e("paymentValueRequired")).integer(e("positiveIntegerOnly")).max(100, e("paymentValueMax")),
        paymentTrigger: a1(),
        projectCompletion: o1().transform((e, t) => "" === t || "Not Applicable" === t ? 0 : e).required(e("projectCompletionRequired")).max(100, e("projectCompletionMax"))
    }),
    ane = e => v1().shape({
        payments: x1().of(rne(e)).max(10, e("paymentsMaximum")).test("sum-equals-100", e("sumMustEqual100"), e => {
            if (!e) return !1;
            return 100 === e.reduce((e, t) => e + (t.paymentValue || 0), 0)
        }).test("ascending-project-completion", e("projectCompletionMustBeAscending"), e => {
            if (!e || e.length < 2) return !0;
            for (let t = 0; t < e.length - 1; t++) {
                const n = e[t].projectCompletion,
                    r = e[t + 1].projectCompletion;
                if (null !== n && null !== r && n >= r) return !1
            }
            return !0
        }).required()
    }),
    ine = 69,
    one = 68;
var sne = (e => (e.Inactive = "Inactive", e.Active = "Active", e.Pending = "Pending", e.Failed = "Failed", e.Success = "Success", e))(sne || {}),
    lne = (e => (e.BasedOnProjectCompletion = "basedOnProjectCompletion", e.AfterContractSigning = "afterContractSigning", e.UponHandover = "uponHandover", e))(lne || {});
const dne = ({
        index: t,
        control: n,
        errors: r,
        onDelete: a,
        canDelete: i,
        isFirst: o,
        isLast: s,
        isLessThanPrev: l,
        hasSumError: d
    }) => {
        const {
            t: c
        } = Gn(), u = ["firstPayment", "secondPayment", "thirdPayment", "fourthPayment", "fifthPayment", "sixthPayment", "seventhPayment", "eighthPayment", "ninthPayment"], p = [{
            name: c("afterContractSigning"),
            id: lne.AfterContractSigning
        }, {
            name: c("basedOnProjectCompletion"),
            id: lne.BasedOnProjectCompletion
        }];
        return e.jsxs(sP, {
            spacing: 10,
            sx: {
                alignItems: "center",
                my: 2
            },
            children: [e.jsx(lP, {
                xs: 12,
                md: 2,
                children: e.jsx(hp, {
                    variant: "body",
                    sx: {
                        mt: 6
                    },
                    children: c((h = t, m = s, m ? "lastPayment" : u[h]))
                })
            }), e.jsx(lP, {
                xs: 12,
                md: 2.5,
                children: e.jsx(Cf, {
                    name: `payments.${t}.paymentValue`,
                    control: n,
                    errors: r,
                    label: c("paymentValue"),
                    type: "number",
                    inputProps: {
                        inputMode: "numeric",
                        step: 1,
                        pattern: "[0-9]*",
                        onKeyDown: e => {
                            ["e", "E", "+", "-", "."].includes(e.key) && e.preventDefault()
                        }
                    },
                    sx: {
                        "& input": {
                            color: d ? "error.main" : "inherit"
                        }
                    }
                })
            }), e.jsx(lP, {
                xs: 12,
                md: 3.5,
                sx: {
                    "& .MuiInputBase-root": {
                        border: "none !important"
                    },
                    "& .MuiOutlinedInput-input": {
                        border: "none !important"
                    },
                    "& svg.MuiSvgIcon-root": {
                        display: "none"
                    }
                },
                children: e.jsx(eg, {
                    name: `payments.${t}.paymentTrigger`,
                    label: c("paymentTrigger"),
                    control: n,
                    errors: r,
                    valueIsObject: !1,
                    options: p || [],
                    disabled: !0
                })
            }), e.jsxs(lP, {
                xs: 12,
                md: 2.5,
                children: [e.jsx(Cf, {
                    name: `payments.${t}.projectCompletion`,
                    control: n,
                    errors: r,
                    label: c("projectCompletion"),
                    disabled: o || s,
                    value: o ? "Not Applicable" : s ? 100 : void 0
                }), !o && !s && l && e.jsx(hp, {
                    variant: "label",
                    color: "error.main",
                    children: c("projectCompletionMustBeGreaterThanPrevious")
                })]
            }), i && e.jsx(lP, {
                xs: 12,
                md: 1.5,
                sx: {
                    display: "flex",
                    justifyContent: "center",
                    mt: 6
                },
                children: e.jsx(wp, {
                    onClick: a,
                    startIcon: e.jsx(th, {}),
                    color: "error",
                    children: e.jsx(hp, {
                        color: "currentColor",
                        bold: !0,
                        sx: {
                            mt: 1
                        },
                        children: c("delete")
                    })
                })
            })]
        });
        var h, m
    },
    cne = ({
        isOpen: t,
        onClose: n,
        onSave: r,
        initialPayments: a
    }) => {
        const {
            t: i
        } = Gn(), {
            form: o,
            fields: s,
            addPayment: l,
            deletePayment: d,
            canAddPayment: c,
            canDeletePayment: u,
            isFirstPayment: p,
            isLastPayment: h
        } = (e => {
            const {
                t: t
            } = Gn(), n = bf({
                mode: "onChange",
                reValidateMode: "onChange",
                resolver: L1(ane(t)),
                defaultValues: {
                    payments: [...e ?? []]
                }
            });
            Dt.useEffect(() => {
                n.reset({
                    payments: [...e ?? []]
                })
            }, [n, JSON.stringify(e)]);
            const {
                fields: r,
                remove: a,
                insert: i
            } = xf({
                control: n.control,
                name: "payments"
            }), o = r.length < 10;
            return {
                form: n,
                fields: r,
                addPayment: () => {
                    const e = n.getValues("payments");
                    if (e.length >= 10) return;
                    const t = {
                        paymentValue: 0,
                        paymentTrigger: lne.BasedOnProjectCompletion,
                        projectCompletion: 0
                    };
                    i(e.length - 1, t)
                },
                deletePayment: e => {
                    n.trigger("payments");
                    const t = n.getValues("payments");
                    t.length <= 2 || 0 !== e && e !== t.length - 1 && a(e)
                },
                canAddPayment: o,
                canDeletePayment: e => !(r.length <= 2) && 0 !== e && e !== r.length - 1,
                isFirstPayment: e => 0 === e,
                isLastPayment: e => e === r.length - 1,
                errors: n.formState.errors
            }
        })(a), m = o.formState.errors.payments?.root?.message;
        return e.jsxs(v, {
            open: t,
            onClose: n,
            maxWidth: "lg",
            fullWidth: !0,
            children: [e.jsxs(ap, {
                sx: {
                    p: "24px 24px 0"
                },
                children: [e.jsx(hp, {
                    variant: "h5",
                    sx: {
                        mb: 8
                    },
                    children: i("editPaymentSchedule")
                }), e.jsx(hp, {
                    variant: "body",
                    color: "text.secondary",
                    bold: !0,
                    children: i("paymentSchedule")
                })]
            }), e.jsxs(ap, {
                children: [e.jsxs(_, {
                    sx: {
                        minHeight: "400px",
                        pt: 6
                    },
                    children: [e.jsx(Cp, {
                        variant: "primary",
                        body: e.jsxs("ul", {
                            children: [e.jsx("li", {
                                children: i("paymentNote1")
                            }), e.jsx("li", {
                                children: i("paymentNote2")
                            }), e.jsx("li", {
                                children: i("paymentNote3")
                            })]
                        })
                    }), e.jsx(km, {
                        ...o,
                        children: e.jsx(ap, {
                            sx: {
                                mb: 3
                            },
                            children: s.map((t, n) => {
                                const r = o.watch("payments"),
                                    a = ((e, t) => {
                                        const n = t[e - 1]?.projectCompletion ? Number(t[e - 1].projectCompletion) : null,
                                            r = t[e]?.projectCompletion ? Number(t[e].projectCompletion) : null;
                                        return e > 0 && null !== n && "number" == typeof r && "number" == typeof n && r <= n
                                    })(n, r);
                                return e.jsx(dne, {
                                    index: n,
                                    control: o.control,
                                    errors: o.formState.errors,
                                    onDelete: () => d(n),
                                    canDelete: u(n),
                                    isFirst: p(n),
                                    isLast: h(n),
                                    hasSumError: !!m,
                                    isLessThanPrev: a
                                }, t.id)
                            })
                        })
                    }), m && e.jsx(Cp, {
                        variant: "error",
                        body: m
                    }), e.jsx(ap, {
                        sx: {
                            display: "flex",
                            justifyContent: "space-between",
                            alignItems: "center",
                            pt: 3,
                            mb: 3
                        },
                        children: c && e.jsx(wp, {
                            variant: "text",
                            onClick: l,
                            startIcon: e.jsx(jf, {}),
                            sx: {
                                px: 0,
                                textDecoration: "underline",
                                fontSize: "16px"
                            },
                            children: i("addNewPayment")
                        })
                    })]
                }), e.jsxs(M, {
                    sx: {
                        borderTop: "1px solid #E3E3E3",
                        p: 18,
                        gap: 2
                    },
                    children: [e.jsx(wp, {
                        variant: "text",
                        onClick: () => {
                            n(), o.reset({
                                payments: a
                            })
                        },
                        color: "error",
                        sx: {
                            minWidth: "255px"
                        },
                        children: i("common.cancel")
                    }), e.jsx(wp, {
                        variant: "hovered",
                        onClick: o.handleSubmit(e => {
                            m || (r(e.payments), n())
                        }),
                        sx: {
                            minWidth: "255px"
                        },
                        children: i("common.save")
                    })]
                })]
            })]
        })