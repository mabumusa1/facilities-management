                    xs: 12,
                    children: e.jsxs(ap, {
                        fullWidth: !0,
                        row: !0,
                        xbetween: !0,
                        ycenter: !0,
                        children: [e.jsxs(ap, {
                            column: !0,
                            gap: 1,
                            children: [e.jsx(hp, {
                                variant: "h5",
                                children: n("generalInformationTitle")
                            }), s && e.jsx(hp, {
                                variant: "smallText",
                                color: "text.secondary",
                                children: n("youCanEditTheGeneralOffPlanSaleDetails")
                            })]
                        }), s && e.jsxs(ap, {
                            row: !0,
                            gap: 4,
                            ycenter: !0,
                            children: [!l && e.jsx(wp, {
                                variant: "outlined",
                                onClick: d,
                                children: n("editLicenseInformation")
                            }), l && e.jsxs(e.Fragment, {
                                children: [e.jsx(wp, {
                                    variant: "outlined",
                                    sx: {
                                        color: "error.main",
                                        borderColor: "error.main"
                                    },
                                    onClick: d,
                                    children: n("cancel")
                                }), e.jsx(wp, {
                                    variant: "contained",
                                    onClick: a.handleSubmit(c),
                                    isLoading: x,
                                    disabled: x,
                                    children: n("saveChanges")
                                })]
                            })]
                        })]
                    })
                }), !s && e.jsxs(Pp, {
                    xs: 4,
                    children: [e.jsx(Y3, {
                        isOpen: M,
                        setIsOpen: S,
                        title: n("rentalApplication.selectCommunity"),
                        selectLabel: n("rentalApplication.communityLabel"),
                        searchPlaceholder: n("contacts.searchPlaceholder"),
                        noDataTitle: n("noTypeFound"),
                        noDataDescription: n("noTypeFound"),
                        placeholder: n("rentalApplication.selectCommunity"),
                        formFieldName: Yte.COMMUNITY,
                        fetcher: T9,
                        refetchKey: `${nF}.${JI}`,
                        value: a.watch(Yte.COMMUNITY),
                        setSelectedValue: e => {
                            a.setValue(Yte.COMMUNITY, e), a.trigger(Yte.COMMUNITY)
                        },
                        CustomRowItem: ({
                            property: t
                        }) => e.jsx(Qte, {
                            item: t
                        }),
                        rightRadioInput: !0,
                        isMultiSelect: !1,
                        disabled: !1
                    }), C?.[Yte.COMMUNITY] && e.jsx(N, {
                        error: !0,
                        children: C?.[Yte.COMMUNITY]?.message
                    })]
                }), e.jsxs(Pp, {
                    xs: 4,
                    children: [e.jsx(Y3, {
                        isOpen: L,
                        setIsOpen: k,
                        title: n("selectEdaatCode"),
                        selectLabel: n("productCodeEdaat"),
                        searchPlaceholder: n("contacts.searchPlaceholder"),
                        noDataTitle: n("noTypeFound"),
                        noDataDescription: n("noTypeFound"),
                        placeholder: n("chooseACode"),
                        formFieldName: Yte.PRODUCT_CODE,
                        fetcher: Bte,
                        refetchKey: `${nF}.${JI}.EDAAT`,
                        value: a.watch(Yte.PRODUCT_CODE),
                        setSelectedValue: e => {
                            a.setValue(Yte.PRODUCT_CODE, e), a.trigger(Yte.PRODUCT_CODE)
                        },
                        CustomRowItem: ({
                            property: t
                        }) => e.jsx(ap, {
                            p: "12px",
                            children: e.jsx(hp, {
                                variant: "label",
                                bold: !0,
                                children: t?.name
                            })
                        }),
                        rightRadioInput: !0,
                        isPaginated: !1,
                        isMultiSelect: !1,
                        disabled: s,
                        allowRetry: !0,
                        hideSearchBar: !0
                    }), C?.[Yte.PRODUCT_CODE] && e.jsx(N, {
                        error: !0,
                        children: C?.[Yte.PRODUCT_CODE]?.message
                    })]
                }), e.jsx(Pp, {
                    xs: s ? 8 : 4,
                    children: e.jsxs(ap, {
                        column: !0,
                        gap: 1,
                        children: [e.jsx(hp, {
                            variant: "smallText",
                            mb: 3,
                            children: n("availablePurchaseMethods")
                        }), e.jsx(j2, {
                            name: Yte.PURCHASE_METHOD,
                            label: n("cashPaymentTitle"),
                            control: a.control,
                            errors: C,
                            disabled: !0
                        })]
                    })
                }), e.jsx(Pp, {
                    xs: 4,
                    children: e.jsx(o$, {
                        name: Yte.LICENSE_NUMBER,
                        label: n("offPlanSalesLicenseNumberWafi"),
                        placeholder: n("enterANumber"),
                        control: a.control,
                        errors: C,
                        disabled: s && !l
                    })
                }), e.jsx(Pp, {
                    xs: 4,
                    children: e.jsx(Ij, {
                        name: Yte.LICENSE_ISSUE_DATE,
                        label: n("licenseIssueDate"),
                        placeholderText: n("pickADate"),
                        control: a.control,
                        errors: C,
                        minDate: i,
                        maxDate: o,
                        disabled: s && !l
                    })
                }), e.jsx(Pp, {
                    xs: 4,
                    children: e.jsx(Ij, {
                        name: Yte.LICENSE_EXPIRY_DATE,
                        label: n("licenseExpiryDate"),
                        placeholderText: n("pickADate"),
                        control: a.control,
                        errors: C,
                        minDate: a.watch(Yte.LICENSE_ISSUE_DATE) || i,
                        maxDate: o,
                        disabled: s && !l
                    })
                })]
            }),
            H = e.jsxs(Pp, {
                xs: 12,
                children: [e.jsxs(ap, {
                    fullWidth: !0,
                    row: !0,
                    xbetween: !0,
                    ycenter: !0,
                    children: [e.jsx(hp, {
                        variant: "h5",
                        mb: 4,
                        children: n("paymentInformation")
                    }), !s && e.jsx(wp, {
                        variant: "contained",
                        onClick: () => j(!0),
                        children: n("edit")
                    })]
                }), s && w > 0 && e.jsx(ap, {
                    mb: 3,
                    children: e.jsx(Cp, {
                        variant: "error",
                        title: n("someRecordsFailed", {
                            count: w
                        }),
                        body: n("pleaseClickAgainOnSendPaymentButton")
                    })
                }), s && v?.some(e => !1 === e.show_btn_send_payment) && e.jsx(ap, {
                    mb: 2,
                    children: e.jsx(Cp, {
                        variant: "primary",
                        body: n("paymentInProgressDisabledNote")
                    })
                }), e.jsx(ap, {
                    column: !0,
                    gap: "12px",
                    children: Vne({
                        payments: v,
                        isPreviewMode: s,
                        handleMarkAsComplete: (e, t) => {
                            m(Wte.MARK_AS_COMPLETE), E.current = () => {
                                f(e, t)
                            }, p(!0)
                        },
                        handleSendPayment: (e, t) => {
                            m(Wte.SEND_PAYMENT), E.current = () => g(e, t), p(!0)
                        },
                        handleNotifyCustomers: (e, t) => {
                            m(Wte.REMIND_CUSTOMER), E.current = () => y(e, t), p(!0)
                        },
                        additional_payments: b?.additional_payments,
                        selected_community_sales_commission_rate: A
                    }).map(t => e.jsx(rte, {
                        ...t
                    }, t.title))
                })]
            }),
            R = e.jsx(lh, {
                isOpen: u,
                onDialogClose: () => p(!1),
                variant: "info",
                content: h === Wte.MARK_AS_COMPLETE ? {
                    title: n("updateStatusTitle"),
                    body: n("updateStatusMessage")
                } : h === Wte.SEND_PAYMENT ? {
                    title: n("sendPaymentTitle"),
                    body: n("sendPaymentMessage")
                } : {
                    title: n("notifyCustomersTitle"),
                    body: n("notifyCustomersMessage")
                },
                closeBtnText: n("common.no"),
                primaryButton: {
                    title: n("common.yes"),
                    variant: "contained",
                    handleClick: () => {
                        E.current && (E.current(), E.current = null)
                    }
                }
            }),
            Y = e.jsx(cne, {
                isOpen: T,
                onClose: () => j(!1),
                initialPayments: v,
                onSave: e => {
                    _(e), a.setValue(Yte.PAYMENTS, e), a.trigger(Yte.PAYMENTS)
                }
            });
        return e.jsxs($te, {
            ...r,
            children: [!s && P, s && I, F, H, R, Y]
        })
    },
    zne = ({
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
    Une = ({
        form: t,
        onSubmit: n,
        children: r,
        isLoading: a,
        btnLoading: i
    }) => a ? e.jsx(cP, {
        xcenter: !0,
        ycenter: !0,
        fullHeight: !0,
        children: e.jsx(d, {})
    }) : e.jsx(km, {
        ...t,
        children: e.jsx(cP, {
            ml: 20,
            mt: 20,
            component: "form",
            onSubmit: t.handleSubmit(n),
            children: e.jsxs(zne, {
                sx: {
                    padding: "36px 36px 36px 18px"
                },
                children: [e.jsx(lP, {
                    xs: 12,
                    children: e.jsx(cP, {
                        mt: -10,
                        children: e.jsx(IQ, {})
                    })
                }), r]
            })
        })
    });
var Wne = (e => (e.PURCHASE_METHOD_CASH = "purchaseMethodCash", e.PURCHASE_METHOD_BANK = "purchaseMethodBank", e))(Wne || {});

function Zne(e) {
    const {
        t: t
    } = Gn(), n = Ys(), [r, a] = Dt.useState(!1), {
        data: i,
        isLoading: o
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
    }), s = v1().shape({
        [Wne.PURCHASE_METHOD_CASH]: qX().default(!1).test("at-least-one-cash", t("pleaseSelectAtLeastOnePurchaseMethod"), function(e) {
            const t = this.parent[Wne.PURCHASE_METHOD_BANK];
            return !!e || !!t
        }),
        [Wne.PURCHASE_METHOD_BANK]: qX().default(!1).test("at-least-one-bank", t("pleaseSelectAtLeastOnePurchaseMethod"), function(e) {
            return !!this.parent[Wne.PURCHASE_METHOD_CASH] || !!e
        })
    }).required(), l = bf({
        defaultValues: {
            [Wne.PURCHASE_METHOD_CASH]: !1,
            [Wne.PURCHASE_METHOD_BANK]: !1
        },
        resolver: L1(s),
        mode: "onChange"
    });
    Dt.useEffect(() => {
        if (i?.data) {
            const e = i.data,
                t = !!e?.allow_cash_sale,
                n = !!e?.allow_bank_financing;
            l.reset({
                [Wne.PURCHASE_METHOD_CASH]: t,
                [Wne.PURCHASE_METHOD_BANK]: n
            })
        }
    }, [e, i, l]);
    const d = nl({
        mutationFn: ({
            communityId: e,
            payload: t
        }) => (async (e, t) => {
            try {
                return await co(`/api-management/marketplace/admin/communities/update-sales-information/${e}`, t)
            } catch (n) {
                throw n
            }
        })(e, t),
        onSuccess: t => {
            Zi.success(t.message), n.invalidateQueries({
                queryKey: [nF, e]
            }), a(!1)
        },
        onError: e => {}
    });
    return {
        form: l,
        onSubmit: async t => {
            try {
                const n = {
                        allow_cash_sale: t[Wne.PURCHASE_METHOD_CASH] ? 1 : 0,
                        allow_bank_financing: t[Wne.PURCHASE_METHOD_BANK] ? 1 : 0
                    },
                    r = Number(e);
                await d.mutateAsync({
                    communityId: r,
                    payload: n
                })
            } catch (n) {}
        },
        isLoading: o,
        btnLoading: d.isPending,
        isEditEnabled: r,
        toggleEdit: () => {
            if (r && i?.data) {
                const e = i.data,
                    t = !!e?.allow_cash_sale,
                    n = !!e?.allow_bank_financing;
                l.reset({
                    [Wne.PURCHASE_METHOD_CASH]: !!t,
                    [Wne.PURCHASE_METHOD_BANK]: !!n
                }, {
                    keepErrors: !1,
                    keepDirty: !1
                })
            }
            a(e => !e)
        },
        salesInformationData: i?.data
    }
}
const qne = ({
        salesCommissionRate: t
    }) => {
        const n = [{
            key: "description",
            header: Jn("paymentDescription"),
            render: (t, n) => {
                const r = e.jsxs(ap, {
                    children: [e.jsx(hp, {
                        variant: "body",
                        children: t ?? "--"
                    }), n.descriptionSubline && e.jsx(hp, {
                        variant: "caption",
                        color: "text.secondary",
                        children: n.descriptionSubline
                    })]
                });
                return n.tooltip && n.tooltip.trim() ? e.jsx(vne, {
                    label: r,
                    tooltipText: n.tooltip
                }) : r
            }
        }, {
            key: "value",
            header: Jn("paymentValue")
        }, {
            key: "trigger",
            header: Jn("paymentTrigger")
        }];
        return [{
            title: Jn("paymentsSchedule"),
            columns: n,
            rows: $ne(t),
            sx: {
                mt: 3
            }
        }]
    },
    $ne = e => {
        const t = e ? +e : 0,
            n = Number.isFinite(t) ? `${t}%` : "0%";
        return [{
            description: Jn("requiredUnitPayment"),
            value: "100%",
            trigger: Jn("afterContractSigning")
        }, {
            description: Jn("unitForm.salesCommissionInclVAT"),
            descriptionSubline: Jn("unitForm.salesCommissionInclVatSubline"),
            value: n,
            trigger: Jn("afterContractSigning"),
            tooltip: Jn("salesInformationCommissionHint")
        }, {
            description: Jn("realEstateTransactionTaxRETT5"),
            descriptionSubline: Jn("unitForm.realEstateTransactionTaxSubline"),
            value: "5%",
            trigger: Jn("uponHandover")
        }]
    },
    Gne = ({
        id: t
    }) => {
        const {
            t: n
        } = Gn(), r = Zne(t), {
            form: a,
            isEditEnabled: i,
            toggleEdit: o,
            onSubmit: s,
            btnLoading: l,
            salesInformationData: d
        } = r, c = a.formState.errors, u = d?.sales_commission_rate, p = d?.listed_percentage, h = e.jsx(Pp, {
            xs: 12,
            children: e.jsx(Gte, {
                title: n("listedUnitsBookingRate"),
                statusLabel: n("bookedUnits"),
                progress: p
            })
        }), m = e.jsxs(e.Fragment, {
            children: [e.jsx(Pp, {
                xs: 12,
                children: e.jsxs(ap, {
                    fullWidth: !0,
                    row: !0,
                    xbetween: !0,
                    ycenter: !0,
                    children: [e.jsxs(ap, {
                        column: !0,
                        gap: 1,
                        children: [e.jsx(hp, {
                            variant: "h5",
                            children: n("generalInformationTitle")
                        }), e.jsx(hp, {
                            variant: "smallText",
                            color: "text.secondary",
                            children: n("youCanEditTheGeneralSaleDetails")
                        })]
                    }), e.jsxs(ap, {
                        row: !0,
                        gap: 4,
                        ycenter: !0,
                        children: [!i && e.jsx(wp, {
                            variant: "outlined",
                            onClick: o,
                            children: n("editSalesInformation")
                        }), i && e.jsxs(e.Fragment, {
                            children: [e.jsx(wp, {
                                variant: "outlined",
                                sx: {
                                    color: "error.main",
                                    borderColor: "error.main"
                                },
                                onClick: o,
                                type: "button",
                                children: n("cancel")
                            }), e.jsx(wp, {
                                variant: "contained",
                                type: "submit",
                                onClick: a.handleSubmit(s),
                                isLoading: l,
                                disabled: l,
                                children: n("saveChanges")
                            })]
                        })]
                    })]
                })
            }), e.jsx(Pp, {
                xs: 12,
                children: e.jsxs(ap, {
                    column: !0,
                    gap: 1,
                    children: [e.jsx(hp, {
                        variant: "smallText",
                        mb: 3,
                        children: n("availablePurchaseMethods")
                    }), e.jsxs(ap, {
                        row: !0,
                        gap: 6,
                        children: [e.jsx(j2, {
                            name: Wne.PURCHASE_METHOD_CASH,
                            label: n("cashPaymentTitle"),
                            control: a.control,
                            errors: c,
                            disabled: !i
                        }), e.jsx(j2, {
                            name: Wne.PURCHASE_METHOD_BANK,
                            label: n("bankFinancing"),
                            control: a.control,
                            errors: c,
                            disabled: !i
                        })]
                    }), (c?.[Wne.PURCHASE_METHOD_CASH] || c?.[Wne.PURCHASE_METHOD_BANK]) && e.jsx(N, {
                        error: !0,
                        children: c?.[Wne.PURCHASE_METHOD_CASH]?.message || c?.[Wne.PURCHASE_METHOD_BANK]?.message
                    })]
                })
            })]
        }), f = e.jsxs(Pp, {
            xs: 12,
            children: [e.jsx(ap, {
                fullWidth: !0,
                row: !0,
                xbetween: !0,
                ycenter: !0,
                children: e.jsx(hp, {
                    variant: "h5",
                    mb: 4,
                    children: n("paymentInformation")
                })
            }), e.jsx(ap, {
                column: !0,
                gap: "12px",
                children: qne({
                    salesCommissionRate: u
                }).map(t => e.jsx(rte, {
                    ...t
                }, t.title))
            })]
        });
        return e.jsxs(Une, {
            ...r,
            children: [h, m, f]
        })
    };

function Kne() {
    const {
        t: t
    } = Gn(), {
        id: n,
        tabStrategy: r,
        activeTab: a,
        unitsStats: i,
        setActiveTab: o,
        handleSearch: s,
        handlePageChange: l,
        search: d,
        page: c,
        unitToBeUnlisted: u,
        setUnitToBeUnlisted: p,
        unlistUnit: h,
        isUnlistingUnitSuccess: m,
        isListAllSuccess: f,
        unitToBeEdited: g,
        setUnitToBeEdited: y,
        editUnit: v,
        isEditUnitSuccess: _,
        isEditing: x,
        isRent: b,
        isAllPricesVisibleLoading: w
    } = Fte(), [C] = $t(), M = Dt.useMemo(() => C.get("communityName") ?? t("communityUnit"), [C]), S = Dt.useMemo(() => "1" === C.get("isOfPlan"), [C]), [L, k] = Dt.useState(m), [T, j] = Dt.useState(f);
    Dt.useEffect(() => {
        m && (p(null), k(!0))
    }, [m]), Dt.useEffect(() => {
        f && j(!0)
    }, [f]), Dt.useEffect(() => {
        _ && y(null)
    }, [_]);
    const E = [{
        title: t("saleInformation"),
        id: a9.SALE_INFORMATION,
        data: null,
        disabled: S
    }, {
        title: t("offPlanSaleInformation"),
        id: a9.OFF_PLAN,
        data: null,
        disabled: !S
    }, {
        title: `${t("marketplace.listedUnits")} (${i?.listed??".."})`,
        id: a9.LISTED,
        data: null
    }, {
        title: `${t("marketplace.notListedUnits")} (${i?.unlisted??".."})`,
        id: a9.UNLISTED,
        data: null
    }];
    return e.jsxs(cP, {
        pl: "16px",
        children: [e.jsxs(rP, {
            s: "24",
            mt: "36px",
            children: [t("common.manage"), " ", M]
        }), e.jsx(gee, {
            sx: {
                mt: "16px",
                mb: "8px"
            },
            alltabs: E?.filter(e => !e.disabled),
            changeTab: o,
            selectedTab: a
        }), a !== a9.OFF_PLAN && a !== a9.SALE_INFORMATION && e.jsxs(e.Fragment, {
            children: [e.jsxs(cP, {
                row: !0,
                sx: {
                    backgroundColor: "#EBF0F1",
                    alignItems: "center",
                    mb: "24px",
                    p: "12px",
                    gap: "12px",
                    borderRadius: "8px"
                },
                children: [e.jsx(z8, {}), e.jsxs(cP, {
                    children: [e.jsx(rP, {
                        s: "14",
                        children: t("Note")
                    }), e.jsx(rP, {
                        s: "12",
                        light: !0,
                        sx: {
                            "& p": {
                                my: "4px"
                            }
                        },
                        children: e.jsx("p", {
                            dangerouslySetInnerHTML: {
                                __html: t(r?.note)
                            }
                        })
                    })]
                })]
            }), e.jsx(o7, {
                RenderTable: e.jsx(E5, {
                    isLoading: r?.isLoading,
                    isEmpty: !r?.list?.length,
                    showEmptyPlaceholder: !1,
                    filters: e.jsxs(cP, {
                        row: !0,
                        sx: {
                            "& fieldset": {
                                border: "none"
                            },
                            "& input": {
                                py: "6px"
                            }
                        },
                        children: [e.jsx(RQ, {
                            search: d,
                            handleSearch: s
                        }), e.jsxs(cP, {
                            row: !0,
                            gap: "12px",
                            children: ["=", " ", r?.filters?.map(t => e.jsx(Ate, {
                                ...t
                            }))]
                        })]
                    }),
                    headerData: [t("leasing.unit"), t("leasing.building"), t("signUp.unitType"), t("properties.add_unit.unit_area"), t(b ? "annualRentalPrice" : "Price"), t("unitForm.bookingDepositInfo"), t("Unit Status"), ...b ? [t("pricesVisibility")] : [], ""],
                    pagination: e.jsxs(cP, {
                        row: !0,
                        gap: "12px",
                        sx: {
                            width: "max-content"
                        },
                        children: [r?.toggleAllVisibilityBtn({
                            isAllPricesVisibleLoading: w
                        }), r?.list?.length > 0 && e.jsx(Qne, {
                            color: r?.btnColor,
                            txt: t(r?.btnText),
                            modalData: r?.confirmationModalData,
                            isLoading: r?.isAllMutating,
                            isSuccess: r?.isMutationAllSuccess,
                            disabled: r?.isAllMutating || r?.btnDisabled
                        })]
                    }),
                    bottomPagination: r?.metadata?.last_page > 1 && e.jsx(cP, {
                        sx: {
                            py: "1rem",
                            "& .MuiPagination-ul li button": {
                                border: "none",
                                backgroundColor: "#F0F0F0",
                                borderRadius: "8px",
                                mr: "8px"
                            },
                            "& .MuiPagination-ul li .Mui-selected": {
                                backgroundColor: "#2E3032",
                                color: "white",
                                borderRadius: "8px"
                            }
                        },
                        children: e.jsx(HQ, {
                            page: c,
                            count: r?.metadata?.last_page,
                            handler: l
                        })
                    }),
                    children: r?.list?.map(t => e.jsx(Nte, {
                        data: t,
                        actions: r?.actions,
                        isRent: b,
                        toggleVisibilityBtn: r?.toggleVisibilityBtn
                    }, t?.id))
                })
            })]
        }), a === a9.SALE_INFORMATION && e.jsx(Gne, {
            id: n
        }), a === a9.OFF_PLAN && e.jsx(Bne, {
            id: n
        }), e.jsx(QW, {
            isOpen: !!u,
            onDialogClose: () => p(null),
            primaryButton: {
                title: t("common.yes"),
                handleClick: () => h({
                    unitId: u
                })
            },
            icon: e.jsx(z8, {
                color: "red",
                width: "100%",
                sx: {
                    width: "70px",
                    height: "70px",
                    borderRadius: "50%"
                }
            }),
            content: {
                title: t("marketplace.unlistAllNote"),
                body: t("marketplace.unlistBody"),
                errors: [],
                actionText: t("common.no")
            },
            clickAction: () => p(null)
        }), e.jsx(QW, {
            isOpen: L,
            onDialogClose: () => k(!1),
            content: {
                title: t("marketplace.unlistSuccess"),
                body: t("marketplace.unlistSuccessBody")
            },
            renderCloseBtn: () => e.jsx(dP, {
                variant: "text",
                fullWidth: !0,
                color: "success",
                onClick: () => k(!1),
                children: t("common.close")
            }),
            clickAction: () => k(!1)
        }), e.jsx(QW, {
            isOpen: T,
            onDialogClose: () => j(!1),
            content: {
                title: t("marketplace.listUnitsSuccess"),
                body: t("marketplace.listUnitsSuccessBody")
            },
            renderCloseBtn: () => e.jsx(dP, {
                variant: "text",
                fullWidth: !0,
                color: "success",
                onClick: () => j(!1),
                children: t("common.close")
            }),
            clickAction: () => j(!1)
        }), g && e.jsx(Rte, {
            handleClose: () => y(null),
            submit: e => {
                v({
                    unitId: g?.id,
                    data: e
                })
            },
            isSubmitting: x,
            defaultValues: g
        })]
    })
}

function Qne({
    txt: t,
    modalData: n,
    isLoading: r,
    isSuccess: a,
    color: i,
    disabled: o
}) {
    const [s, l] = Dt.useState(!1), {
        t: d
    } = Gn();
    return Dt.useEffect(() => {
        a && l(!1)
    }, [a]), e.jsxs(e.Fragment, {
        children: [e.jsx(dP, {
            variant: "contained",
            sx: {
                whiteSpace: "nowrap",
                px: "20px"
            },
            color: i,
            disabled: o,
            onClick: () => l(!0),
            isLoading: r,
            children: t
        }), e.jsx(QW, {
            content: {
                title: d(n.title),
                body: d(n.content, {
                    ...n.data
                }),
                errors: [],
                actionText: d("common.no")
            },
            icon: e.jsx(z8, {
                color: n?.color,
                width: "100%",
                sx: {
                    width: "70px",
                    height: "70px",
                    borderRadius: "50%"
                }
            }),
            onDialogClose: () => l(!1),
            isOpen: s,
            clickAction: () => l(!1),
            primaryButton: {
                title: d("common.yes"),
                color: n.primaryBtnColor,
                handleClick: n.mutate
            }
        })]
    })
}
const Jne = [{
        title: "mp-listing",
        path: "marketplace/listing",
        children: [{
            title: "",
            path: "",
            element: e.jsx(Dte, {})
        }, {
            title: "mp-manage",
            path: "manage/:id",
            element: e.jsx(Kne, {})
        }, {
            title: "offPlanSaleForm",
            path: "off-plan-sale-form",
            element: e.jsx(Bne, {})
        }]
    }, {
        title: "customersTitle",
        path: "marketplace/customers",
        children: [{
            title: "",
            path: "",
            element: e.jsx(ete, {})
        }, {
            title: "customerDetails",
            path: ":customerId",
            element: e.jsx(vte, {})
        }, {
            title: "uploadLeads",
            path: "upload-leads",
            element: e.jsx(Zt, {}),
            children: [{
                title: "",
                path: "",
                element: e.jsx(bte, {})
            }, {
                title: "uploadLeadsErrors",
                path: "errors",
                element: e.jsx(wte, {})
            }]
        }]
    }, {
        title: "visits",
        path: "dashboard/visits",
        element: e.jsx($9, {
            isSales: !0
        })
    }],
    Xne = ({
        spacing: t
    }) => e.jsxs(ap, {
        row: !0,
        ycenter: !0,
        children: [e.jsxs(hp, {
            variant: "body",
            sx: {
                width: "100px"
            },
            children: [t, " = ", 4 * t, "px"]
        }), e.jsx(ap, {
            row: !0,
            gap: "12px",
            sx: {
                backgroundColor: e => u(e.palette.primary.main, .2 * t / 4),
                width: "100%",
                height: e => e.spacing(t)
            }
        })]
    }),
    ere = () => e.jsxs(ap, {
        column: !0,
        gap: "16px",
        m: 10,
        dir: "ltr",
        children: [e.jsxs(e.Fragment, {
            children: [e.jsx(hp, {
                variant: "h4",
                my: 4,
                children: "Typography Variants"
            }), e.jsxs(ap, {
                row: !0,
                gap: "100px",
                children: [e.jsxs(ap, {
                    column: !0,
                    gap: "12px",
                    children: [e.jsx(hp, {
                        variant: "h1",
                        children: "Title (H1)"
                    }), e.jsx(hp, {
                        variant: "h2",
                        children: "Title (H2)"
                    }), e.jsx(hp, {
                        variant: "h3",
                        children: "Title (H3)"
                    }), e.jsx(hp, {
                        variant: "h4",
                        children: "Title (H4)"
                    }), e.jsx(hp, {
                        variant: "h5",
                        children: "Title (H5)"
                    })]
                }), e.jsxs(ap, {
                    column: !0,
                    gap: "12px",
                    children: [e.jsx(hp, {
                        variant: "body",
                        children: "Body text"
                    }), e.jsx(hp, {
                        variant: "label",
                        children: "Label"
                    }), e.jsx(hp, {