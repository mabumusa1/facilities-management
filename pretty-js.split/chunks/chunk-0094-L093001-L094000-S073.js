            sx: {
                color: "#f50057"
            },
            s: "12",
            light: !0,
            "data-testid": "booking-type-error",
            children: i("fieldRequired")
        })]
    })
}

function x5({
    name: t = "working_hours_type",
    workingHours: n,
    control: r,
    errors: a,
    working_hours_type: i,
    setValue: o,
    getValues: s,
    isEdit: l,
    gap: d = "0px",
    size: c = "medium",
    disabled: u = !1,
    enableLabel: p = !1,
    trigger: h
}) {
    const {
        t: m
    } = Gn(), [f, g] = Dt.useState("");
    Dt.useEffect(() => {
        s("start_time") && g(s("start_time"))
    }, []);
    const y = Array.from({
        length: 24
    }, (e, t) => ({
        name: `${t%12==0?12:t%12}:00 ${t<12?"AM":"PM"}`,
        id: `${t<10?`0${t}`:t}:00:00`
    }));
    return e.jsxs(lP, {
        column: !0,
        gap: d,
        mb: "16px",
        children: [e.jsx(lP, {
            md: 12,
            children: e.jsx(rP, {
                variant: "h6",
                mb: "16px",
                children: m("contacts.Set Working Hours")
            })
        }), e.jsx(lP, {
            md: 12,
            children: e.jsx(cP, {
                mt: -10,
                children: e.jsx(d5, {
                    name: t,
                    label: !0,
                    labels: n,
                    control: r,
                    errors: a,
                    row: !0,
                    color: "primary",
                    gap: "60px",
                    labelStyle: b5,
                    size: c,
                    disabled: u
                })
            })
        }), e.jsx(lP, {
            md: 12,
            row: !0,
            mt: "-24px",
            xbetween: !0,
            gap: 10,
            children: i === O6.CUSTOM && e.jsxs(e.Fragment, {
                children: [e.jsxs(lP, {
                    xs: 12,
                    md: 6,
                    children: [p && e.jsx(rP, {
                        light: !0,
                        s: 14,
                        sx: {
                            mb: "8px",
                            color: "#525451"
                        },
                        children: m("startTime")
                    }), e.jsx(oq, {
                        name: "start_time",
                        placeholder: m("startTime"),
                        control: r,
                        errors: a,
                        valueIsObject: !1,
                        disabled: u,
                        options: y,
                        onChange: e => g(e?.target?.value)
                    })]
                }), e.jsxs(lP, {
                    xs: 12,
                    md: 6,
                    children: [p && e.jsx(rP, {
                        light: !0,
                        s: 14,
                        sx: {
                            mb: "8px",
                            color: "#525451"
                        },
                        children: m("endTime")
                    }), e.jsx(oq, {
                        name: "end_time",
                        placeholder: m("endTime"),
                        control: r,
                        errors: a,
                        valueIsObject: !1,
                        disabled: u,
                        options: f ? y?.filter(e => {
                            const [t] = f.split(":").map(Number), [n] = e.id.split(":").map(Number);
                            return t < n
                        }) : y
                    })]
                })]
            })
        })]
    })
}
const b5 = {
    fontSize: "17px !important",
    fontWeight: "500 !important",
    color: "#232425"
};

function w5({
    control: t,
    errors: n,
    setValue: r,
    duration: a
}) {
    const {
        t: i,
        i18n: {
            language: o
        }
    } = Gn(), [s, l] = Dt.useState("");
    Dt.useEffect(() => {
        l(a ? z6(a, i, o) : `30 ${i("serviceSettings.min")}`)
    }, [a]);
    return e.jsxs(e.Fragment, {
        children: [e.jsxs(lP, {
            md: 12,
            children: [e.jsx(rP, {
                variant: "h4",
                s: 18,
                children: i("bookingDuration")
            }), e.jsx(rP, {
                s: 14,
                light: !0,
                gray: !0,
                children: i("facilitiesBooking.bookingDuration_description")
            })]
        }), e.jsx(lP, {
            md: 12,
            children: e.jsx(g5, {
                onChange: e => {
                    const t = z6(e, i, o);
                    l(t), r("reservation_duration", e)
                },
                min: I6.HALF_HOUR,
                max: 24 * I6.ONE_HOUR,
                name: "reservation_duration",
                control: t,
                errors: n,
                value: Number(a),
                renderValue: () => e.jsx(rP, {
                    s: 16,
                    light: !0,
                    children: s
                }),
                changeAmount: 30
            })
        })]
    })
}
const C5 = "CONFIRM_ACTION";

function M5({
    queryKey: t,
    title: n,
    body: r,
    handleClose: a,
    isOpen: i,
    confirmFunc: o,
    to: s,
    hasForm: l,
    showSuccessToast: d = !0
}) {
    const {
        t: c
    } = Gn(), u = Ft(), {
        CurrentBrand: p
    } = Gc(), h = Ys(), m = async () => {
        fq(C5, !0);
        try {
            await (o()?.then(() => {
                d && Zi.success(c("common.success")), a(), fq(C5, !1)
            })), t && h.invalidateQueries(Array.isArray(t) ? t : [t]), s && u(s), a()
        } catch (e) {
            fq(C5, !1), Lo(e, {}, !0)
        }
        fq(C5, !1)
    };
    return e.jsxs(v, {
        onClose: a,
        open: i,
        fullWidth: !0,
        maxWidth: "sm",
        children: [e.jsx(TJ, {
            title: c(""),
            subtitle: "",
            handleClose: a
        }), e.jsx(_, {
            sx: {
                my: "18px",
                mx: "32px"
            },
            children: e.jsxs("form", {
                onSubmit: () => m(),
                children: [e.jsx(cP, {
                    sx: {
                        textAlign: "center",
                        justifyContent: "center",
                        display: "flex"
                    },
                    children: e.jsx(S5, {
                        sx: {
                            width: "67px",
                            height: "67px"
                        },
                        color: qc?.[p]?.primaryPalette?.main
                    })
                }), e.jsx(rP, {
                    "data-testid": "confirmation-dialog-title",
                    align: "center",
                    s: 24,
                    sx: {
                        mt: "22px"
                    },
                    children: n
                }), e.jsx(rP, {
                    variant: "subtitle1",
                    sx: {
                        fontWeight: 400,
                        textAlign: "center"
                    },
                    children: r
                }), e.jsxs(cP, {
                    sx: {
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                        flexDirection: "column",
                        mt: "70px"
                    },
                    children: [e.jsx(a$, {
                        fullWidth: !1,
                        name: C5,
                        sx: {
                            px: "130px",
                            marginBottom: "20px",
                            py: "10px"
                        },
                        type: l ? "submit" : "button",
                        color: "primary",
                        variant: "contained",
                        onClick: m,
                        children: c("ApproveVisitorForm.yes")
                    }), e.jsx(dP, {
                        sx: {
                            px: "130px",
                            marginBottom: "20px",
                            color: "inherit"
                        },
                        onClick: a,
                        children: c("common.no")
                    })]
                })]
            })
        })]
    })
}
const S5 = ({
        color: t = "#008EA5",
        ...n
    }) => e.jsx(i, {
        ...n,
        inheritViewBox: !0,
        children: e.jsx("svg", {
            width: "67",
            height: "67",
            viewBox: "0 0 67 67",
            fill: "none",
            xmlns: "http://www.w3.org/2000/svg",
            children: e.jsx("path", {
                d: "M33.4974 66.8307C15.0874 66.8307 0.164062 51.9074 0.164062 33.4974C0.164062 15.0874 15.0874 0.164062 33.4974 0.164062C51.9074 0.164062 66.8307 15.0874 66.8307 33.4974C66.8307 51.9074 51.9074 66.8307 33.4974 66.8307ZM30.1641 30.1641V50.1641H36.8307V30.1641H30.1641ZM30.1641 16.8307V23.4974H36.8307V16.8307H30.1641Z",
                fill: t
            })
        })
    }),
    L5 = () => {
        const t = Ys(),
            {
                t: n,
                i18n: {
                    language: r
                }
            } = Gn(),
            a = Ft(),
            {
                facilityID: i
            } = qt(),
            [o] = $t(),
            s = !!i,
            [l, d] = Dt.useState(!1),
            c = bf({
                defaultValues: {
                    ...p5
                },
                resolver: L1(u5(n)),
                mode: "onChange"
            }),
            {
                handleSubmit: u,
                formState: {
                    errors: p
                },
                control: h,
                setError: m,
                setValue: f,
                reset: g,
                watch: y,
                clearErrors: v,
                getValues: _,
                register: x,
                trigger: b
            } = c,
            w = y("capacity_limit"),
            C = y("working_hours_type"),
            M = y("booking_type"),
            S = c.getValues();
        y("images"), Dt.useEffect(() => {
            M === O6.SHARED && f("capacity_limit", O6.NO_CAPACITY)
        }, [M]);
        const {
            data: L,
            isLoading: k
        } = tl([eH, i], () => D6(i), {
            enabled: !!i
        });
        Dt.useEffect(() => {
            s && ((e, t) => {
                const n = H6(t);
                Object.entries(n).forEach(([t, n]) => e(t, n))
            })(f, L), c.setValue("complex_id", o.get("complex_id")), c.setValue("approved", Boolean(+L?.approved))
        }, [L, s]);
        const T = P6(n),
            {
                handleDeleteFile: j
            } = JZ(),
            E = async e => {
                const t = _(),
                    n = (e => {
                        const t = {
                            ...e,
                            days: B6(e?.days),
                            capacity: e?.capacity_limit === O6.CUSTOM ? e?.capacity : null,
                            approved: Number(e?.approved)
                        };
                        return e?.booking_type === O6.SHARED && (t.capacity = null, t.reservation_duration = null), e?.working_hours_type === O6.ALL_DAY && (t.start_time = null, t.end_time = null), delete t.capacity_limit, delete t.working_hours_type, delete t.file, delete t.image, t
                    })(t);
                fq(AF, !0);
                const r = t?.images;
                try {
                    const t = await e(n, i);
                    r?.length && r[0].preview ? await (async ({
                        images: e,
                        type: t,
                        id: n,
                        onSuccess: r,
                        onError: a
                    }) => {
                        try {
                            const a = new FormData;
                            e.forEach(e => {
                                a.append("image[]", e)
                            }), a.append("model_type", t), a.append("model_id", Number(n)), a.append("tag", "property_images"), await bo.post("/images/multiple", a), r && r()
                        } catch (i) {
                            a && a(i), i.response
                        }
                    })({
                        images: r,
                        type: "facilities",
                        id: t?.data?.id,
                        onSuccess: () => D(t?.data?.id),
                        onError: V
                    }) : D(+i)
                } catch (a) {
                    V(a)
                }
            }, D = e => {
                g(), t.invalidateQueries([AF]), s && t.invalidateQueries([AF, e]), a(-1), fq(AF, !1)
            }, V = e => {
                Lo(e, {
                    setError: m
                }, !0), fq(AF, !1)
            };
        return k && s ? e.jsx(hP, {}) : e.jsxs(cP, {
            component: "form",
            onSubmit: u(async e => {
                s ? d(!0) : E(j6)
            }),
            children: [e.jsxs(sP, {
                maxWidth: "md",
                spacing: 10,
                children: [e.jsx(lP, {
                    md: 12,
                    component: "header",
                    children: e.jsx(rP, {
                        s: "36",
                        children: n(s ? "facilitiesBooking.edit_title" : "Add New Facility")
                    })
                }), e.jsx(lP, {
                    md: 12,
                    children: e.jsx(_5, {
                        form: c
                    })
                }), e.jsx(lP, {
                    md: 12,
                    children: e.jsx(E4, {
                        label: n("facilityPhoto"),
                        name: "images",
                        files: S.images,
                        maxFileSize: lp,
                        dropzoneText: "",
                        defaultPreview: !0,
                        dropzoneArea: !0,
                        dropZoneAreaComponent: () => e.jsx(yE, {
                            dropZoneText: n("facilityPhoto"),
                            allowedFormats: dp.image,
                            maxFiles: 1,
                            maxFileSize: lp
                        }),
                        acceptedFiles: dp.image,
                        filesLimit: 1,
                        form: {
                            setValue: f,
                            clearErrors: v,
                            trigger: b,
                            register: x,
                            getValues: _
                        },
                        errors: p,
                        onDelete: async e => {
                            try {
                                s && (await j(e?.id), t.invalidateQueries([AF, i]))
                            } catch (n) {
                                Lo(n, {
                                    setError: c.setError
                                })
                            }
                        }
                    })
                }), e.jsx(lP, {
                    md: 6,
                    children: e.jsx(o$, {
                        name: "name_en",
                        label: n("facilitiesBooking.facilityName_en"),
                        placeholder: n("enterFacilityName"),
                        errors: p,
                        control: h,
                        "data-testid": "name_en"
                    })
                }), e.jsx(lP, {
                    md: 6,
                    children: e.jsx(o$, {
                        name: "name_ar",
                        label: n("facilitiesBooking.facilityName_ar"),
                        placeholder: n("enterFacilityName"),
                        errors: p,
                        control: h,
                        "data-testid": "name_ar"
                    })
                }), e.jsx(lP, {
                    md: 12,
                    children: e.jsx(o$, {
                        placeholder: n("enterDetails"),
                        label: n("facilityDescription"),
                        name: "description",
                        errors: p,
                        control: h,
                        multiline: !0,
                        rows: 4
                    })
                }), e.jsx(lP, {
                    md: 12,
                    mt: "14px",
                    children: e.jsx(m5, {
                        control: h,
                        errors: p,
                        workingDays: c5
                    })
                }), e.jsx(x5, {
                    control: h,
                    errors: p,
                    working_hours_type: C,
                    workingHours: T.workingHoursOptions,
                    setValue: f,
                    getValues: _,
                    isEdit: s
                }), M !== O6.SHARED && e.jsxs(e.Fragment, {
                    children: [e.jsx(w5, {
                        setValue: f,
                        duration: S?.reservation_duration,
                        control: h,
                        errors: p
                    }), e.jsx(lP, {
                        md: 12,
                        mt: "10px",
                        children: e.jsx(y5, {
                            capacityLimitOptions: T.capacityLimitOptions,
                            capacity_limit: w,
                            capacity: S?.capacity,
                            control: h,
                            errors: c.getFieldState("capacity_limit").isTouched && p
                        })
                    })]
                }), e.jsxs(lP, {
                    md: 12,
                    children: [e.jsxs(lP, {
                        xs: 12,
                        md: 6,
                        mb: "24px",
                        children: [e.jsxs(cP, {
                            mb: "16px",
                            children: [e.jsx(rP, {
                                variant: "h4",
                                s: 18,
                                children: n("ageLimit")
                            }), e.jsx(rP, {
                                s: 14,
                                light: !0,
                                children: n("facilitiesBooking.age_caption")
                            })]
                        }), e.jsx(o$, {
                            name: "age",
                            errors: p,
                            control: h,
                            placeholder: n("enterAge"),
                            type: "number",
                            InputProps: {
                                inputProps: {
                                    min: 0
                                }
                            }
                        })]
                    }), e.jsxs(lP, {
                        md: 12,
                        children: [e.jsxs(cP, {
                            mb: "16px",
                            mt: "24px",
                            children: [e.jsx(rP, {
                                variant: "h4",
                                s: 18,
                                children: n("facilitiesBooking.allowedGender")
                            }), e.jsx(rP, {
                                s: 14,
                                light: !0,
                                children: n("facilitiesBooking.gender_caption")
                            })]
                        }), e.jsx(d5, {
                            name: "gender",
                            labels: T.genderOptions,
                            control: h,
                            label: !0,
                            errors: p,
                            row: !0,
                            color: "primary",
                            gap: "60px",
                            labelStyle: k5
                        })]
                    })]
                }), e.jsxs(lP, {
                    md: 12,
                    mt: "10px",
                    children: [e.jsx(lP, {
                        children: e.jsx(j2, {
                            label: n("letFacilityApproved"),
                            name: "approved",
                            errors: p,
                            control: h,
                            color: "primary"
                        })
                    }), e.jsx(a$, {
                        name: AF,
                        type: "submit",
                        fullWidth: !1,
                        variant: "contained",
                        sx: {
                            px: "100px",
                            py: "16px",
                            fontSize: "16px",
                            mt: 12
                        },
                        children: n("common.save")
                    })]
                })]
            }), e.jsx(M5, {
                title: n("facilitiesBooking.updateFacilityDialog_header"),
                isOpen: l,
                confirmFunc: () => E(E6),
                showSuccessToast: !1,
                queryKey: [eH, i],
                body: n("facilitiesBooking.updateFacilityDialog_caption"),
                handleClose: () => d(!1)
            })]
        })
    },
    k5 = {
        fontSize: "17px !important",
        fontWeight: "500 !important",
        color: "#232425"
    },
    T5 = Object.freeze(Object.defineProperty({
        __proto__: null,
        default: L5
    }, Symbol.toStringTag, {
        value: "Module"
    })),
    j5 = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACgCAYAAACLz2ctAAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAKWSURBVHgB7d1BTlNBHMfxf6ld6xHwBrqBNN3gDfAE4gk8gycRTwBHYEMIsIAj9Ai4Lo86jRrQHVOTH00/n830rfrS+aYzL3nJVAEAAAAAAAAAAAAAALBpRrWmm5ubN/f391+Wy+VRu9wttsHtaDQ6bfP+fTqdzmsNawV4fX190MI7aR/fFNtoPgzD5xbhWXXqDvD8/PzdZDK5KbbeYrF4P5vNbqvDTnVq8Z0U1HotdAV4eXl5WPZ7PNq9uLg4qA5dAbYN6EHBE+Px+LA6dAXYHjw8dPCv19Whew8I/4MAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiXpVHR4eHo7bcFbwaF4AAAAAAAAAUJ1nxa1OxRmPx7sVNAzDvOeQvKurq6N6wfb29o7rmTZ5PrrehtnZ2Tlqw6cKaj/4cfW9kfOtXrbjeqZNng/vAxIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKIESJQAiRIgUQIkSoBECZAoARIlQKK6AhyNRncFf/tRHboCHIbhtOCJ5XJ5Vh26ApxOp2dtmBf8Mt/f3+/6U+reAy4Wi49tsBTzp4Uu3QHOZrPbthSvvnhebKu71sCHVQvVaa2n4NVSvLqBtv5/bZfdN8HGma/mfDKZvP29HQMAAAAAAAAAAAAAAGCL/ASNh33aj1+SggAAAABJRU5ErkJggg==";

function E5({
    headerData: t,
    children: n,
    pagination: r,
    filters: a,
    isLoading: i,
    isEmpty: o = !0,
    emptyPlaceholder: s,
    showEmptyPlaceholder: l = !1,
    bottomPagination: d,
    noDataTitle: c,
    noDataBody: u
}) {
    const {
        t: p
    } = Gn();
    return e.jsxs(e.Fragment, {
        children: [(a || r) && e.jsx(cP, {
            component: me,
            sx: {
                padding: "16px",
                borderBottom: "1px solid #E3E3E3",
                backgroundColor: "white"
            },
            children: e.jsx(V5, {
                filters: a,
                pagination: r,
                headerData: t,
                asBox: !0
            })
        }), e.jsx(Ct, {
            component: me,
            sx: {
                overflowX: "auto",
                width: "100%",
                "& .MuiTable-root": {
                    minWidth: 700
                }
            },
            children: e.jsx(Mt, {
                sx: {
                    minWidth: 700
                },
                "aria-label": "customized table",
                children: e.jsxs(e.Fragment, {
                    children: [e.jsx(St, {
                        children: i ? e.jsx(Ee, {
                            children: e.jsx(pP, {
                                colSpan: t.length,
                                children: e.jsxs(cP, {
                                    center: !0,
                                    column: !0,
                                    sx: {
                                        py: "64px"
                                    },
                                    children: [e.jsx("img", {
                                        src: j5,
                                        width: "80px",
                                        height: "80px",
                                        alt: "loadingData"
                                    }), e.jsx(hp, {
                                        s: 24,
                                        sx: {
                                            mt: "16px",
                                            fontWeight: 700
                                        },
                                        bold: !0,
                                        variant: "body",
                                        children: p("common.loadingData")
                                    })]
                                })
                            })
                        }) : e.jsx(e.Fragment, {
                            children: o ? e.jsx(Ee, {
                                children: e.jsx(pP, {
                                    colSpan: t.length,
                                    children: e.jsx(D5, {
                                        showEmptyPlaceholder: l,
                                        emptyPlaceholder: s,
                                        noDataTitle: c,
                                        noDataBody: u,
                                        t: p
                                    })
                                })
                            }) : e.jsx(Ee, {
                                children: t.map((t, n) => e.jsx(pP, {
                                    children: e.jsx(hp, {
                                        variant: "body",
                                        bold: !0,
                                        sx: {
                                            textWrap: "nowrap"
                                        },
                                        children: t
                                    })
                                }, n))
                            })
                        })
                    }), !o && !i && e.jsx(Lt, {
                        children: n
                    })]
                })
            })
        }), e.jsx(cP, {
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
            children: !o && d
        })]
    })
}
const D5 = ({
        showEmptyPlaceholder: t,
        emptyPlaceholder: n,
        noDataTitle: r,
        noDataBody: a,
        t: i
    }) => t ? e.jsx(e.Fragment, {
        children: n
    }) : e.jsxs(cP, {
        center: !0,
        column: !0,
        sx: {
            py: "64px"
        },
        children: [e.jsx("img", {
            src: j5,
            width: "80px",
            height: "80px",
            alt: "No data available"
        }), e.jsx(hp, {
            bold: !0,
            s: 24,
            sx: {
                mt: "16px"
            },
            children: r || i("common.NoDataAvailable")
        }), e.jsx(hp, {
            s: 16,
            sx: {
                fontWeight: 400
            },
            children: a || i("common.NoDataAvailableBody")
        })]
    }),
    V5 = ({
        headerData: t,
        filters: n,
        pagination: r,
        asBox: a = !1
    }) => {
        const i = e.jsxs(cP, {
            sx: {
                display: "flex",
                justifyContent: "space-between",
                flexDirection: "row"
            },
            children: [n && e.jsx(cP, {
                fullWidth: !0,
                sx: {
                    display: "flex",
                    alignItems: "center"
                },
                children: n
            }), r && e.jsx(cP, {
                children: r
            })]
        });
        return a ? i : e.jsx(Ee, {
            children: e.jsx(pP, {
                colSpan: t?.length,
                children: i
            })
        })
    };
var A5, O5 = {};

function P5() {
    if (A5) return O5;
    A5 = 1;
    var e = h();
    Object.defineProperty(O5, "__esModule", {
        value: !0
    }), O5.default = void 0;
    var t = e(jp()),
        n = m();
    return O5.default = (0, t.default)((0, n.jsx)("path", {
        d: "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m1 15h-2v-6h2zm0-8h-2V7h2z"
    }), "Info"), O5
}
const I5 = It(P5()),
    F5 = ({
        body: t,
        title: n = "common.warning",
        bg: r = "#FCEDC7",
        iconColor: a = "#FFC225"
    }) => {
        const {
            t: i
        } = Gn();
        return e.jsxs(cP, {
            row: !0,
            ycenter: !0,
            sx: {
                background: r,
                p: "14px",
                borderRadius: "8px"
            },
            children: [e.jsx(cP, {
                children: e.jsx(I5, {
                    style: {
                        color: a,
                        marginRight: "12px",
                        marginLeft: "6px",
                        fontSize: "36px"
                    }
                })
            }), e.jsxs(cP, {
                children: [e.jsx(rP, {
                    style: {
                        fontWeight: 700,
                        fontSize: "14px"
                    },
                    children: i(n)
                }), e.jsx(rP, {
                    style: {
                        fontWeight: 400,
                        mb: "2px",
                        fontSize: "12px"
                    },
                    children: i(t)
                })]
            })]
        })
    },
    H5 = () => {
        const t = Ht(),
            n = t?.state?.excelErrors || [];
        return qt(), e.jsx(Ae, {
            maxWidth: "lg",
            sx: {
                position: "absolute"
            },
            children: e.jsxs(cP, {
                children: [e.jsxs(cP, {
                    sx: {
                        mb: "24px"
                    },
                    children: [e.jsx(IQ, {}), e.jsx(o, {
                        variant: "h4",
                        sx: {
                            my: "24px"
                        },
                        children: Jn("ReviewExcel")
                    }), e.jsx(F5, {
                        title: "Pending issues",
                        body: Jn("pendingIssuesMessage"),
                        bg: "#FFE5E5",
                        iconColor: "#FF4242"
                    })]
                }), e.jsx(E5, {
                    isLoading: !1,
                    isEmpty: !1,
                    headerData: [Jn("ErrorNumber"), Jn("Error message")],
                    children: n.map((t, n) => e.jsxs(uP, {
                        children: [e.jsx(pP, {
                            component: "th",
                            scope: "row",
                            sx: {
                                fontWeight: "bold",
                                width: "0px"
                            },
                            children: n + 1
                        }), e.jsx(pP, {
                            component: "th",
                            scope: "row",
                            sx: {
                                fontWeight: "bold"
                            },
                            children: t
                        })]
                    }, n))
                })]
            })
        })
    },
    N5 = Dt.lazy(() => SZ(() => rr(() => import("./OwnerInfoCU-Cek5P2lT.js"), __vite__mapDeps([98, 1, 2, 3, 6])))),
    R5 = Dt.lazy(() => SZ(() => rr(() => import("./Documents.page-C2CO9-Fj.js"), __vite__mapDeps([99, 1, 2, 3, 6])))),
    Y5 = Dt.lazy(() => SZ(() => rr(() => import("./index-nBf6tIhh.js"), __vite__mapDeps([100, 1, 2, 3, 101, 102, 103, 104, 105, 37, 6])))),
    B5 = Dt.lazy(() => SZ(() => rr(() => import("./AddProperty-BHGq0hSj.js"), __vite__mapDeps([106, 1, 2, 3, 107, 103, 101, 9, 10, 6, 11, 108])))),
    z5 = Dt.lazy(() => SZ(() => rr(() => import("./index-DPed0ZRz.js").then(e => e.i), __vite__mapDeps([109, 1, 2, 3, 105, 37, 10, 108, 103, 101])))),
    U5 = Dt.lazy(() => SZ(() => rr(() => import("./UnitForm-C_m2bGhq.js"), __vite__mapDeps([110, 1, 2, 3, 9, 10, 6, 11, 101, 105, 37, 108, 103])))),
    W5 = Dt.lazy(() => SZ(() => rr(() => import("./UnitDetails-BBgcapny.js"), __vite__mapDeps([111, 1, 2, 3, 27, 10, 109, 105, 37, 108, 103, 101, 102, 6])))),
    Z5 = Dt.lazy(() => SZ(() => rr(() => import("./index-WTiV2rYf.js"), __vite__mapDeps([112, 1, 2, 3, 101, 107, 108, 6])))),
    q5 = Dt.lazy(() => SZ(() => rr(() => import("./AssignBuildingOwner-2O5c2jX0.js"), __vite__mapDeps([113, 1, 2, 3, 6])))),
    $5 = [{
        title: "Property",
        path: "multiUnit/:id",
        children: [{
            title: "AssignNewOwner",
            path: "assign",
            element: e.jsx(q5, {})
        }]
    }, {
        path: "properties-list",
        title: "Property",
        children: [{
            path: "new/:type/:id?",
            title: "add",
            query: "name",
            element: e.jsx(B5, {})
        }, {
            path: ":type/details/:id",
            title: "details",
            query: "name",
            element: e.jsx(Zt, {}),
            children: [{
                path: "",
                title: "community-details",
                element: e.jsx(z5, {})
            }, {
                path: "new",
                title: "edit",
                element: e.jsx(B5, {})
            }, {
                title: "documents",
                path: "documents",
                element: e.jsx(Zt, {}),
                children: [{
                    title: "documents",
                    path: "",
                    element: e.jsx(R5, {})
                }]
            }, {
                title: "addNewFacility",
                path: "addNewFacility/:facilityID?",
                element: e.jsx(L5, {})
            }, {
                title: "facilitiesSettings",
                path: "facilities",
                element: e.jsx(K6, {
                    fromCommunity: !0
                })
            }, {
                title: "facilityDetails",
                path: "facility/:facilityID",
                element: e.jsx(s5, {})
            }]
        }, {
            path: "",
            title: "list",
            query: "name",
            element: e.jsx(Zt, {}),
            children: [{