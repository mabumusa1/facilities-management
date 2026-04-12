                    fid: BA(),
                    registrationStatus: 0
                };
                return rO(t)
            }(n),
            a = function(e, t) {
                if (0 === t.registrationStatus) {
                    if (!navigator.onLine) {
                        return {
                            installationEntry: t,
                            registrationPromise: Promise.reject(VA.create("app-offline"))
                        }
                    }
                    const n = {
                            fid: t.fid,
                            registrationStatus: 1,
                            registrationTime: Date.now()
                        },
                        r = async function(e, t) {
                            try {
                                const n = await async function({
                                    appConfig: e,
                                    heartbeatServiceProvider: t
                                }, {
                                    fid: n
                                }) {
                                    const r = OA(e),
                                        a = FA(e),
                                        i = t.getImmediate({
                                            optional: !0
                                        });
                                    if (i) {
                                        const e = await i.getHeartbeatsHeader();
                                        e && a.append("x-firebase-client", e)
                                    }
                                    const o = {
                                            fid: n,
                                            authVersion: EA,
                                            appId: e.appId,
                                            sdkVersion: jA
                                        },
                                        s = {
                                            method: "POST",
                                            headers: a,
                                            body: JSON.stringify(o)
                                        },
                                        l = await NA(() => fetch(r, s));
                                    if (l.ok) {
                                        const e = await l.json();
                                        return {
                                            fid: e.fid || n,
                                            registrationStatus: 2,
                                            refreshToken: e.refreshToken,
                                            authToken: PA(e.authToken)
                                        }
                                    }
                                    throw await IA("Create Installation", l)
                                }(e, t);
                                return QA(e.appConfig, n)
                            } catch (ti) {
                                throw AA(ti) && 409 === ti.customData.serverCode ? await JA(e.appConfig) : await QA(e.appConfig, {
                                    fid: t.fid,
                                    registrationStatus: 0
                                }), ti
                            }
                        }(e, n);
                    return {
                        installationEntry: n,
                        registrationPromise: r
                    }
                }
                return 1 === t.registrationStatus ? {
                    installationEntry: t,
                    registrationPromise: tO(e)
                } : {
                    installationEntry: t
                }
            }(e, r);
        return t = a.registrationPromise, a.installationEntry
    });
    return "" === n.fid ? {
        installationEntry: await t
    } : {
        installationEntry: n,
        registrationPromise: t
    }
}
async function tO(e) {
    let t = await nO(e.appConfig);
    for (; 1 === t.registrationStatus;) await RA(100), t = await nO(e.appConfig);
    if (0 === t.registrationStatus) {
        const {
            installationEntry: t,
            registrationPromise: n
        } = await eO(e);
        return n || t
    }
    return t
}

function nO(e) {
    return XA(e, e => {
        if (!e) throw VA.create("installation-not-found");
        return rO(e)
    })
}

function rO(e) {
    return 1 === (t = e).registrationStatus && t.registrationTime + TA < Date.now() ? {
        fid: e.fid,
        registrationStatus: 0
    } : e;
    var t;
    /**
     * @license
     * Copyright 2019 Google LLC
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     *   http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     */
}
async function aO({
    appConfig: e,
    heartbeatServiceProvider: t
}, n) {
    const r = function(e, {
        fid: t
    }) {
        return `${OA(e)}/${t}/authTokens:generate`
    }
    /**
     * @license
     * Copyright 2019 Google LLC
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     *   http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     */
    (e, n), a = HA(e, n), i = t.getImmediate({
        optional: !0
    });
    if (i) {
        const e = await i.getHeartbeatsHeader();
        e && a.append("x-firebase-client", e)
    }
    const o = {
            installation: {
                sdkVersion: jA,
                appId: e.appId
            }
        },
        s = {
            method: "POST",
            headers: a,
            body: JSON.stringify(o)
        },
        l = await NA(() => fetch(r, s));
    if (l.ok) {
        return PA(await l.json())
    }
    throw await IA("Generate Auth Token", l)
}
async function iO(e, t = !1) {
    let n;
    const r = await XA(e.appConfig, r => {
        if (!sO(r)) throw VA.create("not-registered");
        const a = r.authToken;
        if (!t && function(e) {
                return 2 === e.requestStatus && ! function(e) {
                    const t = Date.now();
                    return t < e.creationTime || e.creationTime + e.expiresIn < t + DA
                }(e)
            }(a)) return r;
        if (1 === a.requestStatus) return n = async function(e, t) {
            let n = await oO(e.appConfig);
            for (; 1 === n.authToken.requestStatus;) await RA(100), n = await oO(e.appConfig);
            const r = n.authToken;
            return 0 === r.requestStatus ? iO(e, t) : r
        }(e, t), r;
        {
            if (!navigator.onLine) throw VA.create("app-offline");
            const t = function(e) {
                const t = {
                    requestStatus: 1,
                    requestTime: Date.now()
                };
                return Object.assign(Object.assign({}, e), {
                    authToken: t
                })
            }(r);
            return n = async function(e, t) {
                try {
                    const n = await aO(e, t),
                        r = Object.assign(Object.assign({}, t), {
                            authToken: n
                        });
                    return await QA(e.appConfig, r), n
                } catch (ti) {
                    if (!AA(ti) || 401 !== ti.customData.serverCode && 404 !== ti.customData.serverCode) {
                        const n = Object.assign(Object.assign({}, t), {
                            authToken: {
                                requestStatus: 0
                            }
                        });
                        await QA(e.appConfig, n)
                    } else await JA(e.appConfig);
                    throw ti
                }
            }(e, t), t
        }
    });
    return n ? await n : r.authToken
}

function oO(e) {
    return XA(e, e => {
        if (!sO(e)) throw VA.create("not-registered");
        const t = e.authToken;
        return 1 === (n = t).requestStatus && n.requestTime + TA < Date.now() ? Object.assign(Object.assign({}, e), {
            authToken: {
                requestStatus: 0
            }
        }) : e;
        var n;
        /**
         * @license
         * Copyright 2019 Google LLC
         *
         * Licensed under the Apache License, Version 2.0 (the "License");
         * you may not use this file except in compliance with the License.
         * You may obtain a copy of the License at
         *
         *   http://www.apache.org/licenses/LICENSE-2.0
         *
         * Unless required by applicable law or agreed to in writing, software
         * distributed under the License is distributed on an "AS IS" BASIS,
         * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
         * See the License for the specific language governing permissions and
         * limitations under the License.
         */
    })
}

function sO(e) {
    return void 0 !== e && 2 === e.registrationStatus
}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
async function lO(e, t = !1) {
    const n = e;
    await async function(e) {
        const {
            registrationPromise: t
        } = await eO(e);
        t && await t
    }
    /**
     * @license
     * Copyright 2019 Google LLC
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     *   http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     */
    (n);
    return (await iO(n, t)).token
}

function dO(e) {
    return VA.create("missing-app-config-values", {
        valueName: e
    })
}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
const cO = "installations",
    uO = e => {
        const t = uA(e.getProvider("app").getImmediate(), cO).getImmediate();
        return {
            getId: () => async function(e) {
                const t = e,
                    {
                        installationEntry: n,
                        registrationPromise: r
                    } = await eO(t);
                return r ? r.catch(console.error) : iO(t).catch(console.error), n.fid
            }(t),
            getToken: e => lO(t, e)
        }
    };
cA(new eV(cO, e => {
    const t = e.getProvider("app").getImmediate(),
        n = function(e) {
            if (!e || !e.options) throw dO("App Configuration");
            if (!e.name) throw dO("App Name");
            const t = ["projectId", "apiKey", "appId"];
            for (const n of t)
                if (!e.options[n]) throw dO(n);
            return {
                appName: e.name,
                projectId: e.options.projectId,
                apiKey: e.options.apiKey,
                appId: e.options.appId
            }
        }(t);
    return {
        app: t,
        appConfig: n,
        heartbeatServiceProvider: uA(t, "heartbeat"),
        _delete: () => Promise.resolve()
    }
}, "PUBLIC")), cA(new eV("installations-internal", uO, "PRIVATE")), fA(LA, kA), fA(LA, kA, "esm2017");
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
const pO = "BDOU99-h67HcA6JeFXHbSNMu7e2yNNu3RzoMj8TM4W88jITfq7ZmPvIM1Iv-4_l2LxQcYwhqby2xGpWwzjfAnG4",
    hO = "google.c.a.c_id",
    mO = 1e4;
var fO, gO;
/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function yO(e) {
    const t = new Uint8Array(e);
    return btoa(String.fromCharCode(...t)).replace(/=/g, "").replace(/\+/g, "-").replace(/\//g, "_")
}

function vO(e) {
    const t = (e + "=".repeat((4 - e.length % 4) % 4)).replace(/\-/g, "+").replace(/_/g, "/"),
        n = atob(t),
        r = new Uint8Array(n.length);
    for (let a = 0; a < n.length; ++a) r[a] = n.charCodeAt(a);
    return r
}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
! function(e) {
    e[e.DATA_MESSAGE = 1] = "DATA_MESSAGE", e[e.DISPLAY_NOTIFICATION = 3] = "DISPLAY_NOTIFICATION"
}(fO || (fO = {})),
function(e) {
    e.PUSH_RECEIVED = "push-received", e.NOTIFICATION_CLICKED = "notification-clicked"
}(gO || (gO = {}));
const _O = "fcm_token_details_db",
    xO = "fcm_token_object_Store";
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
const bO = "firebase-messaging-store";
let wO = null;

function CO() {
    return wO || (wO = wV("firebase-messaging-database", 1, {
        upgrade: (e, t) => {
            if (0 === t) e.createObjectStore(bO)
        }
    })), wO
}
async function MO(e) {
    const t = LO(e),
        n = await CO(),
        r = await n.transaction(bO).objectStore(bO).get(t);
    if (r) return r;
    {
        const t = await async function(e) {
            if ("databases" in indexedDB) {
                const e = (await indexedDB.databases()).map(e => e.name);
                if (!e.includes(_O)) return null
            }
            let t = null;
            const n = await wV(_O, 5, {
                upgrade: async (n, r, a, i) => {
                    var o;
                    if (r < 2) return;
                    if (!n.objectStoreNames.contains(xO)) return;
                    const s = i.objectStore(xO),
                        l = await s.index("fcmSenderId").get(e);
                    if (await s.clear(), l)
                        if (2 === r) {
                            const e = l;
                            if (!e.auth || !e.p256dh || !e.endpoint) return;
                            t = {
                                token: e.fcmToken,
                                createTime: null !== (o = e.createTime) && void 0 !== o ? o : Date.now(),
                                subscriptionOptions: {
                                    auth: e.auth,
                                    p256dh: e.p256dh,
                                    endpoint: e.endpoint,
                                    swScope: e.swScope,
                                    vapidKey: "string" == typeof e.vapidKey ? e.vapidKey : yO(e.vapidKey)
                                }
                            }
                        } else if (3 === r) {
                        const e = l;
                        t = {
                            token: e.fcmToken,
                            createTime: e.createTime,
                            subscriptionOptions: {
                                auth: yO(e.auth),
                                p256dh: yO(e.p256dh),
                                endpoint: e.endpoint,
                                swScope: e.swScope,
                                vapidKey: yO(e.vapidKey)
                            }
                        }
                    } else if (4 === r) {
                        const e = l;
                        t = {
                            token: e.fcmToken,
                            createTime: e.createTime,
                            subscriptionOptions: {
                                auth: yO(e.auth),
                                p256dh: yO(e.p256dh),
                                endpoint: e.endpoint,
                                swScope: e.swScope,
                                vapidKey: yO(e.vapidKey)
                            }
                        }
                    }
                }
            });
            return n.close(), await CV(_O), await CV("fcm_vapid_details_db"), await CV("undefined"),
                function(e) {
                    if (!e || !e.subscriptionOptions) return !1;
                    const {
                        subscriptionOptions: t
                    } = e;
                    return "number" == typeof e.createTime && e.createTime > 0 && "string" == typeof e.token && e.token.length > 0 && "string" == typeof t.auth && t.auth.length > 0 && "string" == typeof t.p256dh && t.p256dh.length > 0 && "string" == typeof t.endpoint && t.endpoint.length > 0 && "string" == typeof t.swScope && t.swScope.length > 0 && "string" == typeof t.vapidKey && t.vapidKey.length > 0
                }(t) ? t : null
        }(e.appConfig.senderId);
        if (t) return await SO(e, t), t
    }
}
async function SO(e, t) {
    const n = LO(e),
        r = (await CO()).transaction(bO, "readwrite");
    return await r.objectStore(bO).put(t, n), await r.done, t
}

function LO({
    appConfig: e
}) {
    return e.appId
}
/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
const kO = new GD("messaging", "Messaging", {
    "missing-app-config-values": 'Missing App configuration value: "{$valueName}"',
    "only-available-in-window": "This method is available in a Window context.",
    "only-available-in-sw": "This method is available in a service worker context.",
    "permission-default": "The notification permission was not granted and dismissed instead.",
    "permission-blocked": "The notification permission was not granted and blocked instead.",
    "unsupported-browser": "This browser doesn't support the API's required to use the Firebase SDK.",
    "indexed-db-unsupported": "This browser doesn't support indexedDb.open() (ex. Safari iFrame, Firefox Private Browsing, etc)",
    "failed-service-worker-registration": "We are unable to register the default service worker. {$browserErrorMessage}",
    "token-subscribe-failed": "A problem occurred while subscribing the user to FCM: {$errorInfo}",
    "token-subscribe-no-token": "FCM returned no token when subscribing the user to push.",
    "token-unsubscribe-failed": "A problem occurred while unsubscribing the user from FCM: {$errorInfo}",
    "token-update-failed": "A problem occurred while updating the user from FCM: {$errorInfo}",
    "token-update-no-token": "FCM returned no token when updating the user to push.",
    "use-sw-after-get-token": "The useServiceWorker() method may only be called once and must be called before calling getToken() to ensure your service worker is used.",
    "invalid-sw-registration": "The input to useServiceWorker() must be a ServiceWorkerRegistration.",
    "invalid-bg-handler": "The input to setBackgroundMessageHandler() must be a function.",
    "invalid-vapid-key": "The public VAPID key must be a string.",
    "use-vapid-key-after-get-token": "The usePublicVapidKey() method may only be called once and must be called before calling getToken() to ensure your VAPID key is used."
});

function TO({
    projectId: e
}) {
    return `https://fcmregistrations.googleapis.com/v1/projects/${e}/registrations`
}
async function jO({
    appConfig: e,
    installations: t
}) {
    const n = await t.getToken();
    return new Headers({
        "Content-Type": "application/json",
        Accept: "application/json",
        "x-goog-api-key": e.apiKey,
        "x-goog-firebase-installations-auth": `FIS ${n}`
    })
}

function EO({
    p256dh: e,
    auth: t,
    endpoint: n,
    vapidKey: r
}) {
    const a = {
        web: {
            endpoint: n,
            auth: t,
            p256dh: e
        }
    };
    return r !== pO && (a.web.applicationPubKey = r), a
}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
async function DO(e) {
    const t = await async function(e, t) {
        const n = await e.pushManager.getSubscription();
        if (n) return n;
        return e.pushManager.subscribe({
            userVisibleOnly: !0,
            applicationServerKey: vO(t)
        })
    }(e.swRegistration, e.vapidKey), n = {
        vapidKey: e.vapidKey,
        swScope: e.swRegistration.scope,
        endpoint: t.endpoint,
        auth: yO(t.getKey("auth")),
        p256dh: yO(t.getKey("p256dh"))
    }, r = await MO(e.firebaseDependencies);
    if (r) {
        if (function(e, t) {
                const n = t.vapidKey === e.vapidKey,
                    r = t.endpoint === e.endpoint,
                    a = t.auth === e.auth,
                    i = t.p256dh === e.p256dh;
                return n && r && a && i
            }
            /**
             * @license
             * Copyright 2020 Google LLC
             *
             * Licensed under the Apache License, Version 2.0 (the "License");
             * you may not use this file except in compliance with the License.
             * You may obtain a copy of the License at
             *
             *   http://www.apache.org/licenses/LICENSE-2.0
             *
             * Unless required by applicable law or agreed to in writing, software
             * distributed under the License is distributed on an "AS IS" BASIS,
             * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
             * See the License for the specific language governing permissions and
             * limitations under the License.
             */
            (r.subscriptionOptions, n)) return Date.now() >= r.createTime + 6048e5 ? async function(e, t) {
            try {
                const n = await async function(e, t) {
                    const n = await jO(e),
                        r = EO(t.subscriptionOptions),
                        a = {
                            method: "PATCH",
                            headers: n,
                            body: JSON.stringify(r)
                        };
                    let i;
                    try {
                        const n = await fetch(`${TO(e.appConfig)}/${t.token}`, a);
                        i = await n.json()
                    } catch (o) {
                        throw kO.create("token-update-failed", {
                            errorInfo: null == o ? void 0 : o.toString()
                        })
                    }
                    if (i.error) {
                        const e = i.error.message;
                        throw kO.create("token-update-failed", {
                            errorInfo: e
                        })
                    }
                    if (!i.token) throw kO.create("token-update-no-token");
                    return i.token
                }(e.firebaseDependencies, t), r = Object.assign(Object.assign({}, t), {
                    token: n,
                    createTime: Date.now()
                });
                return await SO(e.firebaseDependencies, r), n
            } catch (ti) {
                throw ti
            }
        }(e, {
            token: r.token,
            createTime: Date.now(),
            subscriptionOptions: n
        }): r.token;
        try {
            await async function(e, t) {
                const n = {
                    method: "DELETE",
                    headers: await jO(e)
                };
                try {
                    const r = await fetch(`${TO(e.appConfig)}/${t}`, n),
                        a = await r.json();
                    if (a.error) {
                        const e = a.error.message;
                        throw kO.create("token-unsubscribe-failed", {
                            errorInfo: e
                        })
                    }
                } catch (r) {
                    throw kO.create("token-unsubscribe-failed", {
                        errorInfo: null == r ? void 0 : r.toString()
                    })
                }
            }(e.firebaseDependencies, r.token)
        } catch (ti) {}
        return VO(e.firebaseDependencies, n)
    }
    return VO(e.firebaseDependencies, n)
}
async function VO(e, t) {
    const n =
        /**
         * @license
         * Copyright 2019 Google LLC
         *
         * Licensed under the Apache License, Version 2.0 (the "License");
         * you may not use this file except in compliance with the License.
         * You may obtain a copy of the License at
         *
         *   http://www.apache.org/licenses/LICENSE-2.0
         *
         * Unless required by applicable law or agreed to in writing, software
         * distributed under the License is distributed on an "AS IS" BASIS,
         * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
         * See the License for the specific language governing permissions and
         * limitations under the License.
         */
        await async function(e, t) {
            const n = await jO(e),
                r = EO(t),
                a = {
                    method: "POST",
                    headers: n,
                    body: JSON.stringify(r)
                };
            let i;
            try {
                const t = await fetch(TO(e.appConfig), a);
                i = await t.json()
            } catch (o) {
                throw kO.create("token-subscribe-failed", {
                    errorInfo: null == o ? void 0 : o.toString()
                })
            }
            if (i.error) {
                const e = i.error.message;
                throw kO.create("token-subscribe-failed", {
                    errorInfo: e
                })
            }
            if (!i.token) throw kO.create("token-subscribe-no-token");
            return i.token
        }(e, t), r = {
            token: n,
            createTime: Date.now(),
            subscriptionOptions: t
        };
    return await SO(e, r), r.token
}

function AO(e) {
    const t = {
        from: e.from,
        collapseKey: e.collapse_key,
        messageId: e.fcmMessageId
    };
    return function(e, t) {
            if (!t.notification) return;
            e.notification = {};
            const n = t.notification.title;
            n && (e.notification.title = n);
            const r = t.notification.body;
            r && (e.notification.body = r);
            const a = t.notification.image;
            a && (e.notification.image = a);
            const i = t.notification.icon;
            i && (e.notification.icon = i)
        }(t, e),
        function(e, t) {
            if (!t.data) return;
            e.data = t.data
        }(t, e),
        function(e, t) {
            var n, r, a, i, o;
            if (!t.fcmOptions && !(null === (n = t.notification) || void 0 === n ? void 0 : n.click_action)) return;
            e.fcmOptions = {};
            const s = null !== (a = null === (r = t.fcmOptions) || void 0 === r ? void 0 : r.link) && void 0 !== a ? a : null === (i = t.notification) || void 0 === i ? void 0 : i.click_action;
            s && (e.fcmOptions.link = s);
            const l = null === (o = t.fcmOptions) || void 0 === o ? void 0 : o.analytics_label;
            l && (e.fcmOptions.analyticsLabel = l)
        }
    /**
     * @license
     * Copyright 2019 Google LLC
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     *   http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     */
    (t, e), t
}

function OO(e) {
    return kO.create("missing-app-config-values", {
        valueName: e
    })
}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
class PO {
    constructor(e, t, n) {
        this.deliveryMetricsExportedToBigQueryEnabled = !1, this.onBackgroundMessageHandler = null, this.onMessageHandler = null, this.logEvents = [], this.isLogServiceStarted = !1;
        const r =
            /**
             * @license
             * Copyright 2019 Google LLC
             *
             * Licensed under the Apache License, Version 2.0 (the "License");
             * you may not use this file except in compliance with the License.
             * You may obtain a copy of the License at
             *
             *   http://www.apache.org/licenses/LICENSE-2.0
             *
             * Unless required by applicable law or agreed to in writing, software
             * distributed under the License is distributed on an "AS IS" BASIS,
             * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
             * See the License for the specific language governing permissions and
             * limitations under the License.
             */
            function(e) {
                if (!e || !e.options) throw OO("App Configuration Object");
                if (!e.name) throw OO("App Name");
                const t = ["projectId", "apiKey", "appId", "messagingSenderId"],
                    {
                        options: n
                    } = e;
                for (const r of t)
                    if (!n[r]) throw OO(r);
                return {
                    appName: e.name,
                    projectId: n.projectId,
                    apiKey: n.apiKey,
                    appId: n.appId,
                    senderId: n.messagingSenderId
                }
            }(e);
        this.firebaseDependencies = {
            app: e,
            appConfig: r,
            installations: t,
            analyticsProvider: n
        }
    }
    _delete() {
        return Promise.resolve()
    }
}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
async function IO(e) {
    try {
        e.swRegistration = await navigator.serviceWorker.register("/firebase-messaging-sw.js", {
            scope: "/firebase-cloud-messaging-push-scope"
        }), e.swRegistration.update().catch(() => {}), await async function(e) {
            return new Promise((t, n) => {
                const r = setTimeout(() => n(new Error("Service worker not registered after 10000 ms")), mO),
                    a = e.installing || e.waiting;
                e.active ? (clearTimeout(r), t()) : a ? a.onstatechange = e => {
                    var n;
                    "activated" === (null === (n = e.target) || void 0 === n ? void 0 : n.state) && (a.onstatechange = null, clearTimeout(r), t())
                } : (clearTimeout(r), n(new Error("No incoming service worker found.")))
            })
        }
        /**
         * @license
         * Copyright 2020 Google LLC
         *
         * Licensed under the Apache License, Version 2.0 (the "License");
         * you may not use this file except in compliance with the License.
         * You may obtain a copy of the License at
         *
         *   http://www.apache.org/licenses/LICENSE-2.0
         *
         * Unless required by applicable law or agreed to in writing, software
         * distributed under the License is distributed on an "AS IS" BASIS,
         * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
         * See the License for the specific language governing permissions and
         * limitations under the License.
         */
        (e.swRegistration)
    } catch (ti) {
        throw kO.create("failed-service-worker-registration", {
            browserErrorMessage: null == ti ? void 0 : ti.message
        })
    }
}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
async function FO(e, t) {
    if (!navigator) throw kO.create("only-available-in-window");
    if ("default" === Notification.permission && await Notification.requestPermission(), "granted" !== Notification.permission) throw kO.create("permission-blocked");
    /**
     * @license
     * Copyright 2020 Google LLC
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     *   http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     */
    return await async function(e, t) {
        t ? e.vapidKey = t : e.vapidKey || (e.vapidKey = pO)