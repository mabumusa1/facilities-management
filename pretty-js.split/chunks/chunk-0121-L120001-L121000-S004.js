    };
var une, pne = {};

function hne() {
    if (une) return pne;
    une = 1;
    var e = h();
    Object.defineProperty(pne, "__esModule", {
        value: !0
    }), pne.default = void 0;
    var t = e(jp()),
        n = m();
    return pne.default = (0, t.default)((0, n.jsx)("path", {
        d: "M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2m6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1z"
    }), "Notifications"), pne
}
const mne = It(hne());
var fne = (e => (e.SELECT = "select", e.TEXT = "text", e.COUNTER = "counter", e.RADIO = "radio", e.FILE = "file", e.UI = "ui", e.TEXTAREA = "textarea", e.SEPERATOR = "seperator", e))(fne || {}),
    gne = (e => (e.PHOTOS_IDS = "photos_ids", e.CATEGORY_ID = "category_id", e.TYPE = "type", e.RF_COMMUNITY_ID = "rf_community_id", e.COMMUNITY = "community", e.RF_BUILDING_ID = "rf_building_id", e.NAME = "name", e.ABOUT_THIS_UNIT = "about", e.YEAR_BUILD = "year_build", e.MAP = "map", e.SPECIFICATIONS = "specifications", e[e.AREA = 4] = "AREA", e[e.ELECTRICITY = 11] = "ELECTRICITY", e[e.WATER = 12] = "WATER", e[e.WIDTH = 85] = "WIDTH", e[e.LENGTH = 86] = "LENGTH", e[e.DIRECTION = 87] = "DIRECTION", e[e.STREET_WIDTH = 88] = "STREET_WIDTH", e[e.ELECTRICITY_CONNECTION_STATUS = 89] = "ELECTRICITY_CONNECTION_STATUS", e[e.WATER_CONNECTION_STATUS = 90] = "WATER_CONNECTION_STATUS", e[e.SEWAGE_CONNECTION_STATUS = 91] = "SEWAGE_CONNECTION_STATUS", e.AREA_INDEX = "areaIndex", e.AREAS = "areas", e.NAME_EN = "name_en", e.NAME_AR = "name_ar", e.VALUE = "value", e.ROOM_INDEX = "roomIndex", e.ROOMS = "rooms", e.RENT_INDEX = "rentIndex", e.SALE_INDEX = "saleIndex", e.MARKETPLACE = "marketplace", e.RENT = "rent", e.BUY = "sale", e.RENT_AMOUNT_TYPE = "rentAmountType", e.BUY_AMOUNT_TYPE = "buyAmountType", e.PRICE = "price", e.TAX_RATE = "tax_rate", e.TOTAL_RENT = "totalRental", e.COMMISSION_RATE = "commission_rate", e.SALES_COMMISSION = "salesCommission", e.COMMISSION_VAT = "commission_vat", e.SALES_COMMISSION_WITH_VAT = "salesCommissionWithVAT", e.DEPOSIT = "deposit", e.DOCUMENTS_IDS = "documents_ids", e.FLOOR_PLAN_IDS = "floor_plan_ids", e.FEATURES = "features", e.COMMUNITY_CONTEXT = "communityContext", e.BUILDING_CONTEXT = "buildingContext", e))(gne || {}),
    yne = (e => (e.TOTAL = "total", e.SQM = "sqm", e))(yne || {});
const vne = ({
        label: t,
        tooltipText: n
    }) => e.jsxs(cP, {
        sx: {
            display: "flex",
            alignItems: "center",
            gap: "6px"
        },
        children: [e.jsx(cP, {
            component: "span",
            children: t
        }), e.jsx(y, {
            title: n,
            arrow: !0,
            placement: "top",
            sx: {
                [`& .${xt.tooltip}`]: {
                    backgroundColor: "grey.main",
                    color: "#fff",
                    fontSize: "12px",
                    fontWeight: 400,
                    maxWidth: 250,
                    padding: "4px 10px",
                    borderRadius: "6px"
                }
            },
            children: e.jsx(I5, {
                sx: {
                    fontSize: "18px",
                    color: e => e.palette.primary.light,
                    cursor: "pointer"
                }
            })
        })]
    }),
    _ne = ({
        form: t,
        onSubmit: n,
        children: r,
        isLoading: a,
        btnLoading: i
    }) => {
        const {
            t: o
        } = Gn();
        return a ? e.jsx(cP, {
            xcenter: !0,
            ycenter: !0,
            fullHeight: !0,
            children: e.jsx(d, {})
        }) : e.jsx(km, {
            ...t,
            children: e.jsxs(cP, {
                component: "form",
                onSubmit: t.handleSubmit(n),
                ml: 10,
                mt: 14,
                column: !0,
                gap: "45px",
                children: [e.jsx(cP, {
                    children: e.jsx(IQ, {})
                }), r, e.jsxs(dP, {
                    type: "submit",
                    variant: "contained",
                    sx: {
                        width: "15%",
                        mt: 10,
                        ml: -10
                    },
                    disabled: i,
                    children: [!i && o("common.save"), i && e.jsx(cP, {
                        center: !0,
                        p: 3,
                        children: e.jsx(d, {
                            size: 15
                        })
                    })]
                })]
            })
        })
    },
    xne = ({
        children: t,
        sx: n = {}
    }) => e.jsx(sP, {
        maxWidth: "lg",
        spacing: 10,
        sx: {
            border: "1px solid #E3E3E3",
            borderRadius: "16px",
            padding: "5px 36px 36px 5px",
            background: "white",
            ...n
        },
        children: t
    }),
    bne = ({
        name: t,
        label: n = "",
        placeholder: r = "",
        options: a = [],
        valueIsObject: i = !1,
        disabled: o = !1,
        isDynamic: s = !1,
        deleteByRow: l = !1,
        hidden: d = !1,
        xs: c = 4,
        defaultValue: u = null,
        rules: p = null
    }) => {
        const {
            control: h,
            formState: {
                errors: m
            }
        } = Lm();
        return e.jsx(e.Fragment, {
            children: !d && e.jsx(lP, {
                xs: s && l ? c - .35 : c,
                children: e.jsx(oq, {
                    name: t,
                    label: n,
                    placeholder: r,
                    disabled: o,
                    valueIsObject: i,
                    defaultValue: u,
                    rules: p,
                    options: a,
                    control: h,
                    errors: m
                })
            })
        })
    },
    wne = ({
        name: t,
        label: n = "",
        placeholder: r = "",
        disabled: a = !1,
        isDynamic: i = !1,
        deleteByRow: o = !1,
        hidden: s = !1,
        xs: l = 4,
        onClick: d,
        defaultValue: c = null,
        rules: u = null,
        multiline: p = !1,
        limit: h = 5e4,
        endAdornment: m = null,
        labelTooltip: f = null
    }) => {
        const {
            control: g,
            formState: {
                errors: y
            }
        } = Lm(), v = f ? e.jsx(vne, {
            label: n,
            tooltipText: f
        }) : n;
        return e.jsx(e.Fragment, {
            children: !s && e.jsx(lP, {
                xs: i && o ? l - .35 : l,
                children: e.jsx(o$, {
                    name: t,
                    label: v,
                    placeholder: r,
                    control: g,
                    errors: y,
                    disabled: a,
                    onClick: () => d?.(),
                    inputProps: {
                        readOnly: !!d
                    },
                    defaultValue: c,
                    rules: u,
                    multiline: p,
                    limit: h,
                    InputProps: m ? {
                        endAdornment: m,
                        sx: {
                            backgroundColor: a ? "#F0F0F0 !important" : "transparent"
                        }
                    } : void 0
                })
            })
        })
    },
    Cne = ({
        name: t,
        label: n = "",
        min: r = 0,
        max: a = 10,
        hidden: i = !1,
        isDynamic: o = !1,
        deleteByRow: s = !1,
        xs: l = 4,
        defaultValue: d = null,
        rules: c = null
    }) => {
        const {
            control: u,
            formState: {
                errors: p
            }
        } = Lm();
        return e.jsx(e.Fragment, {
            children: !i && e.jsx(lP, {
                xs: o && s ? l - .35 : l,
                children: e.jsx(g5, {
                    name: t,
                    label: n,
                    control: u,
                    errors: p,
                    dynamicWidth: !0,
                    min: r,
                    max: a,
                    defaultValue: d,
                    rules: c
                })
            })
        })
    },
    Mne = ({
        name: t,
        label: n = "",
        xs: r = 4,
        labels: a = [],
        hidden: i = !1,
        defaultValue: o = null,
        rules: s = null
    }) => {
        const {
            control: l,
            formState: {
                errors: d
            }
        } = Lm();
        return e.jsx(e.Fragment, {
            children: !i && e.jsx(lP, {
                xs: r,
                children: e.jsx(d5, {
                    name: t,
                    color: "primary",
                    label: n,
                    labels: a,
                    row: !0,
                    labelTextStyle: {
                        fontSize: "14px !important",
                        fontWeight: "400",
                        color: "#525451"
                    },
                    labelStyle: {
                        fontSize: "14px !important",
                        fontWeight: "400"
                    },
                    containerStyle: {
                        ml: 4,
                        mt: 1
                    },
                    control: l,
                    errors: d,
                    defaultValue: o,
                    rules: s,
                    size: "small"
                })
            })
        })
    },
    Sne = ({
        name: t,
        max: n = 10,
        xs: r = 12,
        maxFileSize: a = 3e7,
        customErrors: i,
        formats: o = ["image/png", "image/gif", "image/jpeg", "image/webp", "image/avif", "image/jpg", "image/svg", "image/heif", "image/heif", "image/heic"],
        hidden: s = !1,
        DropZoneArea: l = () => null
    }) => {
        const d = Lm();
        return e.jsx(e.Fragment, {
            children: !s && e.jsx(lP, {
                xs: r,
                children: e.jsx(I8, {
                    acceptedFiles: o,
                    filesLimit: n,
                    files: d.getValues(t),
                    maxFileSize: a,
                    onFileSelect: e => ((e, t) => d.setValue(e, t))(t, e),
                    onDelete: e => {
                        return n = t, r = e, d.setValue(n, d.getValues(n)?.filter(e => +e?.id !== +r));
                        var n, r
                    },
                    customErrors: i,
                    dropZoneArea: l
                })
            })
        })
    },
    Lne = ({
        Component: t = () => null,
        xs: n = 4,
        hidden: r = !1
    }) => !r && e.jsx(lP, {
        xs: n,
        children: e.jsx(t, {})
    }),
    kne = ({
        inputs: t = [],
        isDynamic: n = !1,
        deleteByRow: r = !1,
        indexKey: a = "index"
    }) => {
        const i = Lm(),
            o = i.watch(a),
            s = n ? Array.from({
                length: o
            }, (e, n) => t?.map(e => ({
                ...e,
                name: e?.name?.replace("index", n)
            }))).flat() : t;
        return e.jsx(e.Fragment, {
            children: s?.filter(e => !e.hidden)?.map((t, s) => e.jsxs(e.Fragment, {
                children: [t.type === fne.SELECT ? e.jsx(bne, {
                    name: t.name,
                    label: t.label,
                    placeholder: t.placeholder,
                    defaultValue: t.defaultValue,
                    options: t.options,
                    valueIsObject: t.valueIsObject,
                    rules: t.rules,
                    disabled: t?.disabled,
                    isDynamic: n,
                    deleteByRow: r,
                    xs: t.xs
                }, s) : t.type === fne.TEXT ? e.jsx(wne, {
                    name: t.name,
                    label: t.label,
                    placeholder: t.placeholder,
                    defaultValue: t.defaultValue,
                    rules: t.rules,
                    disabled: t?.disabled,
                    isDynamic: n,
                    deleteByRow: r,
                    xs: t.xs,
                    onClick: t.onClick,
                    endAdornment: t.endAdornment,
                    labelTooltip: t.labelTooltip
                }, t.key || s) : t.type === fne.COUNTER ? e.jsx(Cne, {
                    name: t.name,
                    label: t.label,
                    defaultValue: t.defaultValue,
                    rules: t.rules,
                    min: t.min,
                    max: t.max,
                    isDynamic: n,
                    deleteByRow: r,
                    xs: t.xs
                }, s) : t.type === fne.RADIO ? e.jsx(Mne, {
                    name: t.name,
                    label: t.label,
                    labels: t.labels,
                    defaultValue: t.defaultValue,
                    rules: t.rules,
                    xs: t.xs
                }, s) : t.type === fne.FILE ? e.jsx(Sne, {
                    name: t.name,
                    max: t.max,
                    customErrors: t.customErrors,
                    maxFileSize: t.maxFileSize,
                    formats: t.formats,
                    DropZoneArea: t.DropZoneArea,
                    xs: t.xs
                }, s) : t.type === fne.UI ? e.jsx(Lne, {
                    Component: t.Component,
                    xs: t.xs,
                    hidden: t.hidden
                }, s) : t.type === fne.TEXTAREA ? e.jsx(wne, {
                    name: t.name,
                    label: t.label,
                    placeholder: t.placeholder,
                    defaultValue: t.defaultValue,
                    disabled: t?.disabled,
                    multiline: !0,
                    xs: t.xs,
                    limit: t.limit
                }, s) : t.type === fne.SEPERATOR ? e.jsx(lP, {
                    xs: 12,
                    my: -12,
                    children: e.jsx(cP, {})
                }) : e.jsx(e.Fragment, {}), n && r && !!o && (s + 1) % 3 == 0 && e.jsx(lP, {
                    xs: 1,
                    children: e.jsx(w, {
                        onClick: () => {
                            const e = (s + 1) / 3,
                                t = a?.replace("Index", "s"),
                                n = i.watch(t);
                            i.setValue(a, o - 1), i.setValue(t, n?.filter((t, n) => n !== e - 1))
                        },
                        sx: {
                            mt: 13,
                            "& svg": {
                                width: 20,
                                height: 20
                            }
                        },
                        children: e.jsx(EN, {})
                    })
                })]
            }))
        })
    },
    Tne = ({
        title: t,
        description: n,
        indexKey: r = "index",
        inputs: a = [],
        isDynamic: i = !1,
        deleteByRow: o = !1,
        maxRows: s = 5,
        hidden: l = !1
    }) => {
        const {
            t: d
        } = Gn(), c = Lm(), u = c.watch(r);
        return e.jsx(e.Fragment, {
            children: !l && e.jsxs(xne, {
                children: [e.jsxs(lP, {
                    xs: i ? 11 : 12,
                    children: [e.jsx(hp, {
                        variant: "h5",
                        children: t
                    }), n && e.jsx(hp, {
                        s: 16,
                        light: !0,
                        children: n
                    })]
                }), i && !u && e.jsx(lP, {
                    xs: 1,
                    children: e.jsx(dP, {
                        variant: "text",
                        startIcon: e.jsx(jf, {
                            sx: {
                                pb: 1
                            }
                        }),
                        onClick: () => {
                            if (c.setValue(r, u + 1), r === gne.RENT_INDEX) {
                                const e = c.getValues(gne.COMMUNITY),
                                    t = c.getValues(`${gne.MARKETPLACE}.${gne.RENT}`);
                                if (e?.rental_commission_rate) {
                                    const n = e.rental_commission_rate || "",
                                        r = {
                                            ...t[1],
                                            [gne.COMMISSION_RATE]: n
                                        };
                                    c.setValue(`${gne.MARKETPLACE}.${gne.RENT}`, [r])
                                }
                            } else if (r === gne.SALE_INDEX) {
                                const e = c.getValues(gne.COMMUNITY),
                                    t = c.getValues(`${gne.MARKETPLACE}.${gne.BUY}`);
                                if (e?.sales_commission_rate) {
                                    const n = e.sales_commission_rate || "",
                                        r = {
                                            ...t[1],
                                            [gne.COMMISSION_RATE]: n
                                        };
                                    c.setValue(`${gne.MARKETPLACE}.${gne.BUY}`, [r])
                                }
                            }
                        },
                        children: d("unitForm.add")
                    })
                }), !!u && !o && e.jsx(lP, {
                    xs: 1,
                    children: e.jsx(dP, {
                        variant: "text",
                        startIcon: e.jsx(Af, {
                            sx: {
                                pb: 1,
                                ml: -10
                            }
                        }),
                        onClick: () => {
                            const e = r?.replace("Index", "");
                            c.setValue(r, u - 1), c.setValue(`${gne.MARKETPLACE}.${e}`, [])
                        },
                        sx: {
                            color: "red"
                        },
                        children: d("unitForm.remove")
                    })
                }), e.jsx(kne, {
                    inputs: a,
                    isDynamic: i,
                    indexKey: r,
                    deleteByRow: o
                }), i && !!u && u < s && e.jsx(lP, {
                    xs: 12,
                    children: e.jsx(dP, {
                        variant: "text",
                        startIcon: e.jsx(jf, {
                            sx: {
                                pb: 1
                            }
                        }),
                        onClick: () => c.setValue(r, u < s ? u + 1 : u),
                        children: t
                    })
                })]
            })
        })
    },
    jne = ({
        hidden: t = !1
    }) => {
        const {
            t: n
        } = Gn();
        return t ? e.jsx(e.Fragment, {}) : e.jsx(cP, {
            width: "fit-content",
            center: !0,
            padding: "10px",
            sx: {
                backgroundColor: e => e.palette.primary.light + "20",
                borderRadius: "8px",
                marginTop: "-20px",
                marginBottom: "-10px"
            },
            children: e.jsx(hp, {
                s: 12,
                light: !0,
                color: "#969798",
                children: n("unitForm.perSqmDesc")
            })
        })
    },
    Ene = ({
        title: t,
        value: n
    }) => e.jsxs(cP, {
        column: !0,
        fullWidth: !0,
        sx: {
            minHeight: "53px",
            padding: "7px 0px",
            paddingLeft: "4px",
            marginTop: "28px",
            backgroundColor: e => e.palette.primary.light + "20",
            borderRadius: "8px",
            paddingRight: "100px"
        },
        children: [e.jsx(hp, {
            light: !0,
            width: 200,
            s: 12,
            children: t
        }), e.jsx(hp, {
            s: 16,
            currency: !0,
            bold: !0,
            children: n ? d6(Number(n).toFixed(2)) : 0
        })]
    }),
    Dne = ({
        value: t,
        onClick: n,
        isRequired: r = !1,
        form: a
    }) => {
        const {
            t: i,
            i18n: {
                language: o
            }
        } = Gn(), s = a?.formState?.errors?.[gne.MAP], l = "ar" === o;
        return e.jsxs(cP, {
            column: !0,
            gap: "4px",
            onClick: n,
            children: [e.jsxs(hp, {
                variant: "caption",
                sx: {
                    fontWeight: 400,
                    fontSize: "14px"
                },
                children: [i("booking.Location"), " ", r ? "*" : ""]
            }), e.jsxs(hp, {
                variant: "subtitle1",
                value: i("Select Location"),
                sx: {
                    px: 4,
                    height: "54px",
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                    borderRadius: "8px",
                    border: "1px solid #E3E3E3",
                    background: "#ffffff",
                    cursor: "pointer"
                },
                children: [e.jsx(hp, {
                    variant: "body",
                    sx: {
                        overflow: "hidden",
                        textOverflow: "ellipsis",
                        whiteSpace: "nowrap",
                        fontWeight: 300
                    },
                    children: t || i("announcements.addLocation")
                }), l ? e.jsx(xW, {
                    fontSize: "small"
                }) : e.jsx(gW, {
                    fontSize: "small"
                })]
            }), s && e.jsx(hp, {
                variant: "label",
                light: !0,
                color: "error",
                children: s?.message
            })]
        })
    },
    Vne = ({
        payments: t,
        isPreviewMode: n = !1,
        handleMarkAsComplete: r,
        handleSendPayment: a,
        handleNotifyCustomers: i,
        additional_payments: o,
        selected_community_sales_commission_rate: s
    }) => {
        const l = o?.find(e => "rett" === e.type)?.value_percent ?? "5",
            d = o?.find(e => "commission_vat" === e.type)?.value_percent ?? s ?? "--",
            c = [{
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
            }, {
                key: "completion",
                header: Jn("projectCompletion")
            }],
            u = n ? [...c, {
                key: "status",
                header: Jn("projectCompletionStatus"),
                render: (t, n, r) => 0 === r ? e.jsx(hp, {
                    variant: "body",
                    children: Jn("leaseForm.notApplicable")
                }) : One(n.statusId)
            }, {
                key: "actions",
                header: Jn("leasing.actions"),
                render: (e, n, o) => Pne(n, o, t, r, a, i)
            }] : c;
        return [{
            title: Jn("paymentsSchedule"),
            columns: u,
            rows: Ane(t, n),
            sx: {
                mt: 3
            }
        }, {
            title: Jn("additionalPayments"),
            columns: c,
            rows: [{
                description: Jn("realEstateTransactionTaxRETT5"),
                descriptionSubline: Jn("unitForm.realEstateTransactionTaxSubline"),
                value: l ? `${l}%` : "--",
                trigger: Jn(lne.UponHandover),
                completion: "100%",
                tooltip: Jn("realEstateTransactionTaxTooltip")
            }, {
                description: Jn("unitForm.commissionRate"),
                descriptionSubline: Jn("unitForm.salesCommissionInclVatSubline"),
                value: "--" !== d ? +d + "%" : "--",
                trigger: Jn(lne.AfterContractSigning),
                completion: Jn("leaseForm.notApplicable")
            }],
            sx: {
                mt: 4
            }
        }]
    },
    Ane = (e, t = !1) => {
        const n = ["firstPayment", "secondPayment", "thirdPayment", "fourthPayment", "fifthPayment", "sixthPayment", "seventhPayment", "eighthPayment", "ninthPayment"];
        return e.map((r, a) => {
            const i = a === e.length - 1,
                o = {
                    description: Jn(i ? "lastPayment" : n[a] || `payment${a+1}`),
                    value: `${r.paymentValue}%`,
                    trigger: r.paymentTrigger === lne.AfterContractSigning ? Jn("afterContractSigning") : r.paymentTrigger === lne.BasedOnProjectCompletion ? Jn("basedOnProjectCompletion") : r.paymentTrigger,
                    completion: "number" == typeof r.projectCompletion ? 0 === r.projectCompletion ? Jn("leaseForm.notApplicable") : `${r.projectCompletion}%` : r.projectCompletion === Jn("leaseForm.notApplicable") || "Not Applicable" === r.projectCompletion || null === r.projectCompletion ? Jn("leaseForm.notApplicable") : r.projectCompletion?.toString() || ""
                };
            return t && (o.paymentId = r.id || `payment-${a}`, o.paymentTrigger = r.paymentTrigger, o.statusId = r.status?.id || one, o.originalPayment = r, o.index = a, o.show_btn_send_payment = r.show_btn_send_payment, o.show_btn_send_reminder = r.show_btn_send_reminder, o.all_unit_payments_count = r.all_unit_payments_count, o.is_sent_count = r.is_sent_count, o.is_success_count = r.is_success_count, o.is_fail_count = r.is_fail_count, o.bulk_payment_status = r.bulk_payment_status), o
        })
    },
    One = t => {
        const n = t === ine;
        return e.jsx(rh, {
            title: Jn(n ? "completed" : "pending"),
            variant: n ? "success" : "warning"
        })
    },
    Pne = (t, n, r, a, i, o) => {
        const {
            paymentTrigger: s,
            statusId: l,
            paymentId: d,
            originalPayment: c
        } = t, u = l === ine, p = s === lne.BasedOnProjectCompletion, h = c?.bulk_payment_status || sne.Inactive, m = p && !u && n >= 1 && Ine(n, r), f = u && p && h !== sne.Inactive;
        if (!m && !f) return null;
        const g = Number(c?.all_unit_payments_count || 0),
            y = Number(c?.is_sent_count || 0),
            v = Number(c?.is_success_count || 0),
            _ = Hne(h),
            x = Rne(h),
            b = Yne(h),
            w = Fne(h, y, g, v),
            C = h === sne.Pending || h === sne.Success || h === sne.Failed;
        return e.jsxs(ap, {
            row: !0,
            gap: 2,
            ycenter: !0,
            children: [m && a && e.jsx(wp, {
                variant: "contained",
                color: "primary",
                onClick: () => a(d || `payment-${n}`, n),
                sx: {
                    minWidth: "auto",
                    px: 6,
                    fontWeight: 700
                },
                children: Jn("updateCompletionPercent")
            }), f && e.jsxs(e.Fragment, {
                children: [e.jsxs(ap, {
                    row: !0,
                    gap: 1,
                    ycenter: !0,
                    children: [i && e.jsx(wp, {
                        variant: "text",
                        color: "primary",
                        disabled: x,
                        onClick: () => i(d || `payment-${n}`, n),
                        startIcon: Nne(h),
                        sx: {
                            minWidth: "auto",
                            px: "2px",
                            fontWeight: 700,
                            textDecoration: "underline",
                            ...h === sne.Failed && {
                                color: "#FFC225"
                            }
                        },
                        children: _
                    }), C && e.jsx(Kp, {
                        title: w,
                        variant: "primary",
                        children: e.jsx(KN.InformationLineIcon, {
                            sx: {
                                width: 14,
                                height: 14,
                                color: "info.main",
                                cursor: "pointer"
                            }
                        })
                    })]
                }), b && o && e.jsx(wp, {
                    variant: "text",
                    onClick: () => o(d || `payment-${n}`, n),
                    startIcon: e.jsx(mne, {
                        sx: {
                            width: 14,
                            height: 14
                        }
                    }),
                    sx: {
                        minWidth: "auto",
                        px: "2px",
                        color: "primary.main"
                    },
                    children: e.jsx(hp, {
                        variant: "caption",
                        color: "primary",
                        sx: {
                            textDecoration: "underline"
                        },
                        bold: !0,
                        children: Jn("sendReminders")
                    })
                })]
            })]
        })
    },
    Ine = (e, t) => {
        for (let n = 1; n < t.length; n++) {
            const r = t[n],
                a = r.status?.id;
            if (r.paymentTrigger === lne.BasedOnProjectCompletion && a !== ine) return n === e
        }
        return !1
    },
    Fne = (e, t, n, r) => {
        switch (e) {
            case sne.Pending:
                return Jn("inProgressPayments", {
                    sent: t,
                    total: n
                });
            case sne.Success:
                return Jn("allPaymentsSentSuccessfully", {
                    success: r,
                    total: n
                });
            case sne.Failed:
                return Jn("partialPaymentsSent", {
                    success: r,
                    total: n
                });
            default:
                return ""
        }
    },
    Hne = t => {
        switch (t) {
            case sne.Pending:
                return e.jsx(hp, {
                    variant: "body",
                    color: "primary",
                    bold: !0,
                    s: 14,
                    children: Jn("issuingPayments")
                });
            case sne.Success:
                return e.jsx(hp, {
                    variant: "body",
                    color: "#1EC27B",
                    bold: !0,
                    s: 14,
                    children: Jn("paymentsIssued")
                });
            case sne.Failed:
                return e.jsx(hp, {
                    variant: "body",
                    color: "#FFC225",
                    bold: !0,
                    s: 14,
                    children: Jn("issuePendingPayments")
                });
            case sne.Active:
            default:
                return e.jsx(hp, {
                    variant: "body",
                    color: "primary",
                    bold: !0,
                    s: 14,
                    children: Jn("issuePayments")
                })
        }
    },
    Nne = t => {
        switch (t) {
            case sne.Pending:
                return e.jsx(KN.ChatHistoryLineIcon, {
                    sx: {
                        width: 16,
                        height: 16
                    }
                });
            case sne.Failed:
                return e.jsx(KN.RestartLineIcon, {
                    sx: {
                        width: 16,
                        height: 16
                    }
                });
            case sne.Success:
                return e.jsx(KN.CheckLineIcon, {
                    sx: {
                        width: 16,
                        height: 16
                    }
                });
            case sne.Inactive:
            case sne.Active:
            default:
                return e.jsx(KN.MailSendLineIcon, {
                    sx: {
                        width: 16,
                        height: 16
                    }
                })
        }
    },
    Rne = e => {
        const t = e;
        return t === sne.Inactive || t === sne.Pending || t === sne.Success
    },
    Yne = e => {
        const t = e;
        return t === sne.Success || t === sne.Failed
    },
    Bne = ({
        id: t
    }) => {
        const {
            t: n
        } = Gn(), r = Zte(t), {
            form: a,
            minDate: i,
            maxDate: o,
            isPreviewMode: s,
            isEditEnabled: l,
            toggleEdit: d,
            onSubmit: c,
            isConfirmOpen: u,
            setIsConfirmOpen: p,
            confirmationType: h,
            setConfirmationType: m,
            handleMarkAsComplete: f,
            handleSendPayment: g,
            handleNotifyCustomers: y,
            payments: v,
            setPayments: _,
            btnLoading: x,
            offPlanData: b,
            failedPaymentsCount: w
        } = r, C = a.formState.errors, [M, S] = Dt.useState(!1), [L, k] = Dt.useState(!1), [T, j] = Dt.useState(!1), E = Dt.useRef(null), D = [{
            id: "1",
            paymentValue: 50,
            paymentTrigger: lne.AfterContractSigning,
            projectCompletion: 0
        }, {
            id: "2",
            paymentValue: 50,
            paymentTrigger: lne.BasedOnProjectCompletion,
            projectCompletion: 100
        }];
        Dt.useEffect(() => {
            s || v && 0 !== v.length || (_(D), a.setValue(Yte.PAYMENTS, D))
        }, [s, v, _, a]);
        const V = a.watch(Yte.COMMUNITY),
            A = V?.[0]?.sales_commission_rate,
            O = +b?.completion_percent,
            P = e.jsx(Pp, {
                xs: 12,
                children: e.jsx(hp, {
                    variant: "h4",
                    children: n("offPlanSaleInformation")
                })
            }),
            I = e.jsx(Pp, {
                xs: 12,
                children: e.jsx(Gte, {
                    progress: O
                })
            }),
            F = e.jsxs(e.Fragment, {
                children: [e.jsx(Pp, {