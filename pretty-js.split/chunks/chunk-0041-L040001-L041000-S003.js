        if (t.isComponentSet()) throw new Error(`Component ${e.name} has already been registered with ${this.name}`);
        t.setComponent(e)
    }
    addOrOverwriteComponent(e) {
        this.getProvider(e.name).isComponentSet() && this.providers.delete(e.name), this.addComponent(e)
    }
    getProvider(e) {
        if (this.providers.has(e)) return this.providers.get(e);
        const t = new nV(e, this);
        return this.providers.set(e, t), t
    }
    getProviders() {
        return Array.from(this.providers.values())
    }
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
var iV;
! function(e) {
    e[e.DEBUG = 0] = "DEBUG", e[e.VERBOSE = 1] = "VERBOSE", e[e.INFO = 2] = "INFO", e[e.WARN = 3] = "WARN", e[e.ERROR = 4] = "ERROR", e[e.SILENT = 5] = "SILENT"
}(iV || (iV = {}));
const oV = {
        debug: iV.DEBUG,
        verbose: iV.VERBOSE,
        info: iV.INFO,
        warn: iV.WARN,
        error: iV.ERROR,
        silent: iV.SILENT
    },
    sV = iV.INFO,
    lV = {
        [iV.DEBUG]: "log",
        [iV.VERBOSE]: "log",
        [iV.INFO]: "info",
        [iV.WARN]: "warn",
        [iV.ERROR]: "error"
    },
    dV = (e, t, ...n) => {
        if (t < e.logLevel) return;
        (new Date).toISOString();
        if (!lV[t]) throw new Error(`Attempted to log a message with an invalid logType (value: ${t})`)
    };
let cV, uV;
const pV = new WeakMap,
    hV = new WeakMap,
    mV = new WeakMap,
    fV = new WeakMap,
    gV = new WeakMap;
let yV = {
    get(e, t, n) {
        if (e instanceof IDBTransaction) {
            if ("done" === t) return hV.get(e);
            if ("objectStoreNames" === t) return e.objectStoreNames || mV.get(e);
            if ("store" === t) return n.objectStoreNames[1] ? void 0 : n.objectStore(n.objectStoreNames[0])
        }
        return xV(e[t])
    },
    set: (e, t, n) => (e[t] = n, !0),
    has: (e, t) => e instanceof IDBTransaction && ("done" === t || "store" === t) || t in e
};

function vV(e) {
    return e !== IDBDatabase.prototype.transaction || "objectStoreNames" in IDBTransaction.prototype ? (uV || (uV = [IDBCursor.prototype.advance, IDBCursor.prototype.continue, IDBCursor.prototype.continuePrimaryKey])).includes(e) ? function(...t) {
        return e.apply(bV(this), t), xV(pV.get(this))
    } : function(...t) {
        return xV(e.apply(bV(this), t))
    } : function(t, ...n) {
        const r = e.call(bV(this), t, ...n);
        return mV.set(r, t.sort ? t.sort() : [t]), xV(r)
    }
}

function _V(e) {
    return "function" == typeof e ? vV(e) : (e instanceof IDBTransaction && function(e) {
        if (hV.has(e)) return;
        const t = new Promise((t, n) => {
            const r = () => {
                    e.removeEventListener("complete", a), e.removeEventListener("error", i), e.removeEventListener("abort", i)
                },
                a = () => {
                    t(), r()
                },
                i = () => {
                    n(e.error || new DOMException("AbortError", "AbortError")), r()
                };
            e.addEventListener("complete", a), e.addEventListener("error", i), e.addEventListener("abort", i)
        });
        hV.set(e, t)
    }(e), ((e, t) => t.some(t => e instanceof t))(e, cV || (cV = [IDBDatabase, IDBObjectStore, IDBIndex, IDBCursor, IDBTransaction])) ? new Proxy(e, yV) : e)
}

function xV(e) {
    if (e instanceof IDBRequest) return function(e) {
        const t = new Promise((t, n) => {
            const r = () => {
                    e.removeEventListener("success", a), e.removeEventListener("error", i)
                },
                a = () => {
                    t(xV(e.result)), r()
                },
                i = () => {
                    n(e.error), r()
                };
            e.addEventListener("success", a), e.addEventListener("error", i)
        });
        return t.then(t => {
            t instanceof IDBCursor && pV.set(t, e)
        }).catch(() => {}), gV.set(t, e), t
    }(e);
    if (fV.has(e)) return fV.get(e);
    const t = _V(e);
    return t !== e && (fV.set(e, t), gV.set(t, e)), t
}
const bV = e => gV.get(e);

function wV(e, t, {
    blocked: n,
    upgrade: r,
    blocking: a,
    terminated: i
} = {}) {
    const o = indexedDB.open(e, t),
        s = xV(o);
    return r && o.addEventListener("upgradeneeded", e => {
        r(xV(o.result), e.oldVersion, e.newVersion, xV(o.transaction), e)
    }), n && o.addEventListener("blocked", e => n(e.oldVersion, e.newVersion, e)), s.then(e => {
        i && e.addEventListener("close", () => i()), a && e.addEventListener("versionchange", e => a(e.oldVersion, e.newVersion, e))
    }).catch(() => {}), s
}

function CV(e, {
    blocked: t
} = {}) {
    const n = indexedDB.deleteDatabase(e);
    return t && n.addEventListener("blocked", e => t(e.oldVersion, e)), xV(n).then(() => {})
}
const MV = ["get", "getKey", "getAll", "getAllKeys", "count"],
    SV = ["put", "add", "delete", "clear"],
    LV = new Map;

function kV(e, t) {
    if (!(e instanceof IDBDatabase) || t in e || "string" != typeof t) return;
    if (LV.get(t)) return LV.get(t);
    const n = t.replace(/FromIndex$/, ""),
        r = t !== n,
        a = SV.includes(n);
    if (!(n in (r ? IDBIndex : IDBObjectStore).prototype) || !a && !MV.includes(n)) return;
    const i = async function(e, ...t) {
        const i = this.transaction(e, a ? "readwrite" : "readonly");
        let o = i.store;
        return r && (o = o.index(t.shift())), (await Promise.all([o[n](...t), a && i.done]))[0]
    };
    return LV.set(t, i), i
}
yV = (e => ({
    ...e,
    get: (t, n, r) => kV(t, n) || e.get(t, n, r),
    has: (t, n) => !!kV(t, n) || e.has(t, n)
}))(yV);
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
class TV {
    constructor(e) {
        this.container = e
    }
    getPlatformInfoString() {
        return this.container.getProviders().map(e => {
            if (function(e) {
                    const t = e.getComponent();
                    return "VERSION" === (null == t ? void 0 : t.type)
                }(e)) {
                const t = e.getImmediate();
                return `${t.library}/${t.version}`
            }
            return null
        }).filter(e => e).join(" ")
    }
}
const jV = "@firebase/app",
    EV = "0.13.2",
    DV = new class {
        constructor(e) {
            this.name = e, this._logLevel = sV, this._logHandler = dV, this._userLogHandler = null
        }
        get logLevel() {
            return this._logLevel
        }
        set logLevel(e) {
            if (!(e in iV)) throw new TypeError(`Invalid value "${e}" assigned to \`logLevel\``);
            this._logLevel = e
        }
        setLogLevel(e) {
            this._logLevel = "string" == typeof e ? oV[e] : e
        }
        get logHandler() {
            return this._logHandler
        }
        set logHandler(e) {
            if ("function" != typeof e) throw new TypeError("Value assigned to `logHandler` must be a function");
            this._logHandler = e
        }
        get userLogHandler() {
            return this._userLogHandler
        }
        set userLogHandler(e) {
            this._userLogHandler = e
        }
        debug(...e) {
            this._userLogHandler && this._userLogHandler(this, iV.DEBUG, ...e), this._logHandler(this, iV.DEBUG, ...e)
        }
        log(...e) {
            this._userLogHandler && this._userLogHandler(this, iV.VERBOSE, ...e), this._logHandler(this, iV.VERBOSE, ...e)
        }
        info(...e) {
            this._userLogHandler && this._userLogHandler(this, iV.INFO, ...e), this._logHandler(this, iV.INFO, ...e)
        }
        warn(...e) {
            this._userLogHandler && this._userLogHandler(this, iV.WARN, ...e), this._logHandler(this, iV.WARN, ...e)
        }
        error(...e) {
            this._userLogHandler && this._userLogHandler(this, iV.ERROR, ...e), this._logHandler(this, iV.ERROR, ...e)
        }
    }("@firebase/app"),
    VV = "@firebase/app-compat",
    AV = "@firebase/analytics-compat",
    OV = "@firebase/analytics",
    PV = "@firebase/app-check-compat",
    IV = "@firebase/app-check",
    FV = "@firebase/auth",
    HV = "@firebase/auth-compat",
    NV = "@firebase/database",
    RV = "@firebase/data-connect",
    YV = "@firebase/database-compat",
    BV = "@firebase/functions",
    zV = "@firebase/functions-compat",
    UV = "@firebase/installations",
    WV = "@firebase/installations-compat",
    ZV = "@firebase/messaging",
    qV = "@firebase/messaging-compat",
    $V = "@firebase/performance",
    GV = "@firebase/performance-compat",
    KV = "@firebase/remote-config",
    QV = "@firebase/remote-config-compat",
    JV = "@firebase/storage",
    XV = "@firebase/storage-compat",
    eA = "@firebase/firestore",
    tA = "@firebase/ai",
    nA = "@firebase/firestore-compat",
    rA = "firebase",
    aA = "[DEFAULT]",
    iA = {
        [jV]: "fire-core",
        [VV]: "fire-core-compat",
        [OV]: "fire-analytics",
        [AV]: "fire-analytics-compat",
        [IV]: "fire-app-check",
        [PV]: "fire-app-check-compat",
        [FV]: "fire-auth",
        [HV]: "fire-auth-compat",
        [NV]: "fire-rtdb",
        [RV]: "fire-data-connect",
        [YV]: "fire-rtdb-compat",
        [BV]: "fire-fn",
        [zV]: "fire-fn-compat",
        [UV]: "fire-iid",
        [WV]: "fire-iid-compat",
        [ZV]: "fire-fcm",
        [qV]: "fire-fcm-compat",
        [$V]: "fire-perf",
        [GV]: "fire-perf-compat",
        [KV]: "fire-rc",
        [QV]: "fire-rc-compat",
        [JV]: "fire-gcs",
        [XV]: "fire-gcs-compat",
        [eA]: "fire-fst",
        [nA]: "fire-fst-compat",
        [tA]: "fire-vertex",
        "fire-js": "fire-js",
        [rA]: "fire-js-all"
    },
    oA = new Map,
    sA = new Map,
    lA = new Map;

function dA(e, t) {
    try {
        e.container.addComponent(t)
    } catch (ti) {
        DV.debug(`Component ${t.name} failed to register with FirebaseApp ${e.name}`, ti)
    }
}

function cA(e) {
    const t = e.name;
    if (lA.has(t)) return DV.debug(`There were multiple attempts to register component ${t}.`), !1;
    lA.set(t, e);
    for (const n of oA.values()) dA(n, e);
    for (const n of sA.values()) dA(n, e);
    return !0
}

function uA(e, t) {
    const n = e.container.getProvider("heartbeat").getImmediate({
        optional: !0
    });
    return n && n.triggerHeartbeat(), e.container.getProvider(t)
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
const pA = new GD("app", "Firebase", {
    "no-app": "No Firebase App '{$appName}' has been created - call initializeApp() first",
    "bad-app-name": "Illegal App name: '{$appName}'",
    "duplicate-app": "Firebase App named '{$appName}' already exists with different options or config",
    "app-deleted": "Firebase App named '{$appName}' already deleted",
    "server-app-deleted": "Firebase Server App has been deleted",
    "no-options": "Need to provide options, when not being deployed to hosting via source.",
    "invalid-app-argument": "firebase.{$appName}() takes either no argument or a Firebase App instance.",
    "invalid-log-argument": "First argument to `onLog` must be null or a function.",
    "idb-open": "Error thrown when opening IndexedDB. Original error: {$originalErrorMessage}.",
    "idb-get": "Error thrown when reading from IndexedDB. Original error: {$originalErrorMessage}.",
    "idb-set": "Error thrown when writing to IndexedDB. Original error: {$originalErrorMessage}.",
    "idb-delete": "Error thrown when deleting from IndexedDB. Original error: {$originalErrorMessage}.",
    "finalization-registry-not-supported": "FirebaseServerApp deleteOnDeref field defined but the JS runtime does not support FinalizationRegistry.",
    "invalid-server-app-environment": "FirebaseServerApp is not for use in browser environments."
});
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
class hA {
    constructor(e, t, n) {
        this._isDeleted = !1, this._options = Object.assign({}, e), this._config = Object.assign({}, t), this._name = t.name, this._automaticDataCollectionEnabled = t.automaticDataCollectionEnabled, this._container = n, this.container.addComponent(new eV("app", () => this, "PUBLIC"))
    }
    get automaticDataCollectionEnabled() {
        return this.checkDestroyed(), this._automaticDataCollectionEnabled
    }
    set automaticDataCollectionEnabled(e) {
        this.checkDestroyed(), this._automaticDataCollectionEnabled = e
    }
    get name() {
        return this.checkDestroyed(), this._name
    }
    get options() {
        return this.checkDestroyed(), this._options
    }
    get config() {
        return this.checkDestroyed(), this._config
    }
    get container() {
        return this._container
    }
    get isDeleted() {
        return this._isDeleted
    }
    set isDeleted(e) {
        this._isDeleted = e
    }
    checkDestroyed() {
        if (this.isDeleted) throw pA.create("app-deleted", {
            appName: this._name
        })
    }
}

function mA(e, t = {}) {
    let n = e;
    if ("object" != typeof t) {
        t = {
            name: t
        }
    }
    const r = Object.assign({
            name: aA,
            automaticDataCollectionEnabled: !0
        }, t),
        a = r.name;
    if ("string" != typeof a || !a) throw pA.create("bad-app-name", {
        appName: String(a)
    });
    if (n || (n = UD()), !n) throw pA.create("no-options");
    const i = oA.get(a);
    if (i) {
        if (QD(n, i.options) && QD(r, i.config)) return i;
        throw pA.create("duplicate-app", {
            appName: a
        })
    }
    const o = new aV(a);
    for (const l of lA.values()) o.addComponent(l);
    const s = new hA(n, r, o);
    return oA.set(a, s), s
}

function fA(e, t, n) {
    var r;
    let a = null !== (r = iA[e]) && void 0 !== r ? r : e;
    n && (a += `-${n}`);
    const i = a.match(/\s|\//),
        o = t.match(/\s|\//);
    if (i || o) {
        const e = [`Unable to register library "${a}" with version "${t}":`];
        return i && e.push(`library name "${a}" contains illegal characters (whitespace or "/")`), i && o && e.push("and"), o && e.push(`version name "${t}" contains illegal characters (whitespace or "/")`), void DV.warn(e.join(" "))
    }
    cA(new eV(`${a}-version`, () => ({
        library: a,
        version: t
    }), "VERSION"))
}
/**
 * @license
 * Copyright 2021 Google LLC
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
const gA = "firebase-heartbeat-store";
let yA = null;

function vA() {
    return yA || (yA = wV("firebase-heartbeat-database", 1, {
        upgrade: (e, t) => {
            if (0 === t) try {
                e.createObjectStore(gA)
            } catch (ti) {}
        }
    }).catch(e => {
        throw pA.create("idb-open", {
            originalErrorMessage: e.message
        })
    })), yA
}
async function _A(e, t) {
    try {
        const n = (await vA()).transaction(gA, "readwrite"),
            r = n.objectStore(gA);
        await r.put(t, xA(e)), await n.done
    } catch (ti) {
        if (ti instanceof $D) DV.warn(ti.message);
        else {
            const t = pA.create("idb-set", {
                originalErrorMessage: null == ti ? void 0 : ti.message
            });
            DV.warn(t.message)
        }
    }
}

function xA(e) {
    return `${e.name}!${e.options.appId}`
}
/**
 * @license
 * Copyright 2021 Google LLC
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
class bA {
    constructor(e) {
        this.container = e, this._heartbeatsCache = null;
        const t = this.container.getProvider("app").getImmediate();
        this._storage = new CA(t), this._heartbeatsCachePromise = this._storage.read().then(e => (this._heartbeatsCache = e, e))
    }
    async triggerHeartbeat() {
        var e, t;
        try {
            const n = this.container.getProvider("platform-logger").getImmediate().getPlatformInfoString(),
                r = wA();
            if (null == (null === (e = this._heartbeatsCache) || void 0 === e ? void 0 : e.heartbeats) && (this._heartbeatsCache = await this._heartbeatsCachePromise, null == (null === (t = this._heartbeatsCache) || void 0 === t ? void 0 : t.heartbeats))) return;
            if (this._heartbeatsCache.lastSentHeartbeatDate === r || this._heartbeatsCache.heartbeats.some(e => e.date === r)) return;
            if (this._heartbeatsCache.heartbeats.push({
                    date: r,
                    agent: n
                }), this._heartbeatsCache.heartbeats.length > 30) {
                const e = function(e) {
                    if (0 === e.length) return -1;
                    let t = 0,
                        n = e[0].date;
                    for (let r = 1; r < e.length; r++) e[r].date < n && (n = e[r].date, t = r);
                    return t
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
                (this._heartbeatsCache.heartbeats);
                this._heartbeatsCache.heartbeats.splice(e, 1)
            }
            return this._storage.overwrite(this._heartbeatsCache)
        } catch (ti) {
            DV.warn(ti)
        }
    }
    async getHeartbeatsHeader() {
        var e;
        try {
            if (null === this._heartbeatsCache && await this._heartbeatsCachePromise, null == (null === (e = this._heartbeatsCache) || void 0 === e ? void 0 : e.heartbeats) || 0 === this._heartbeatsCache.heartbeats.length) return "";
            const t = wA(),
                {
                    heartbeatsToSend: n,
                    unsentEntries: r
                } = function(e, t = 1024) {
                    const n = [];
                    let r = e.slice();
                    for (const a of e) {
                        const e = n.find(e => e.agent === a.agent);
                        if (e) {
                            if (e.dates.push(a.date), MA(n) > t) {
                                e.dates.pop();
                                break
                            }
                        } else if (n.push({
                                agent: a.agent,
                                dates: [a.date]
                            }), MA(n) > t) {
                            n.pop();
                            break
                        }
                        r = r.slice(1)
                    }
                    return {
                        heartbeatsToSend: n,
                        unsentEntries: r
                    }
                }(this._heartbeatsCache.heartbeats),
                a = RD(JSON.stringify({
                    version: 2,
                    heartbeats: n
                }));
            return this._heartbeatsCache.lastSentHeartbeatDate = t, r.length > 0 ? (this._heartbeatsCache.heartbeats = r, await this._storage.overwrite(this._heartbeatsCache)) : (this._heartbeatsCache.heartbeats = [], this._storage.overwrite(this._heartbeatsCache)), a
        } catch (ti) {
            return DV.warn(ti), ""
        }
    }
}

function wA() {
    return (new Date).toISOString().substring(0, 10)
}
class CA {
    constructor(e) {
        this.app = e, this._canUseIndexedDBPromise = this.runIndexedDBEnvironmentCheck()
    }
    async runIndexedDBEnvironmentCheck() {
        return !!ZD() && qD().then(() => !0).catch(() => !1)
    }
    async read() {
        if (await this._canUseIndexedDBPromise) {
            const e = await async function(e) {
                try {
                    const t = (await vA()).transaction(gA),
                        n = await t.objectStore(gA).get(xA(e));
                    return await t.done, n
                } catch (ti) {
                    if (ti instanceof $D) DV.warn(ti.message);
                    else {
                        const t = pA.create("idb-get", {
                            originalErrorMessage: null == ti ? void 0 : ti.message
                        });
                        DV.warn(t.message)
                    }
                }
            }(this.app);
            return (null == e ? void 0 : e.heartbeats) ? e : {
                heartbeats: []
            }
        }
        return {
            heartbeats: []
        }
    }
    async overwrite(e) {
        var t;
        if (await this._canUseIndexedDBPromise) {
            const n = await this.read();
            return _A(this.app, {
                lastSentHeartbeatDate: null !== (t = e.lastSentHeartbeatDate) && void 0 !== t ? t : n.lastSentHeartbeatDate,
                heartbeats: e.heartbeats
            })
        }
    }
    async add(e) {
        var t;
        if (await this._canUseIndexedDBPromise) {
            const n = await this.read();
            return _A(this.app, {
                lastSentHeartbeatDate: null !== (t = e.lastSentHeartbeatDate) && void 0 !== t ? t : n.lastSentHeartbeatDate,
                heartbeats: [...n.heartbeats, ...e.heartbeats]
            })
        }
    }
}

function MA(e) {
    return RD(JSON.stringify({
        version: 2,
        heartbeats: e
    })).length
}
var SA;
SA = "", cA(new eV("platform-logger", e => new TV(e), "PRIVATE")), cA(new eV("heartbeat", e => new bA(e), "PRIVATE")), fA(jV, EV, SA), fA(jV, EV, "esm2017"), fA("fire-js", "");
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
fA("firebase", "11.10.0", "app");
const LA = "@firebase/installations",
    kA = "0.6.18",
    TA = 1e4,
    jA = `w:${kA}`,
    EA = "FIS_v2",
    DA = 36e5,
    VA = new GD("installations", "Installations", {
        "missing-app-config-values": 'Missing App configuration value: "{$valueName}"',
        "not-registered": "Firebase Installation is not registered.",
        "installation-not-found": "Firebase Installation not found.",
        "request-failed": '{$requestName} request failed with error "{$serverCode} {$serverStatus}: {$serverMessage}"',
        "app-offline": "Could not process request. Application offline.",
        "delete-pending-registration": "Can't delete installation while there is a pending registration request."
    });

function AA(e) {
    return e instanceof $D && e.code.includes("request-failed")
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
function OA({
    projectId: e
}) {
    return `https://firebaseinstallations.googleapis.com/v1/projects/${e}/installations`
}

function PA(e) {
    return {
        token: e.token,
        requestStatus: 2,
        expiresIn: (t = e.expiresIn, Number(t.replace("s", "000"))),
        creationTime: Date.now()
    };
    var t
}
async function IA(e, t) {
    const n = (await t.json()).error;
    return VA.create("request-failed", {
        requestName: e,
        serverCode: n.code,
        serverMessage: n.message,
        serverStatus: n.status
    })
}

function FA({
    apiKey: e
}) {
    return new Headers({
        "Content-Type": "application/json",
        Accept: "application/json",
        "x-goog-api-key": e
    })
}

function HA(e, {
    refreshToken: t
}) {
    const n = FA(e);
    return n.append("Authorization", function(e) {
            return `${EA} ${e}`
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
        (t)), n
}
async function NA(e) {
    const t = await e();
    return t.status >= 500 && t.status < 600 ? e() : t
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
function RA(e) {
    return new Promise(t => {
        setTimeout(t, e)
    })
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
const YA = /^[cdef][\w-]{21}$/;

function BA() {
    try {
        const e = new Uint8Array(17);
        (self.crypto || self.msCrypto).getRandomValues(e), e[0] = 112 + e[0] % 16;
        const t = function(e) {
            const t = function(e) {
                return btoa(String.fromCharCode(...e)).replace(/\+/g, "-").replace(/\//g, "_")
            }(e);
            return t.substr(0, 22)
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
        (e);
        return YA.test(t) ? t : ""
    } catch (Gb) {
        return ""
    }
}

function zA(e) {
    return `${e.appName}!${e.appId}`
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
const UA = new Map;

function WA(e, t) {
    const n = zA(e);
    ZA(n, t),
        function(e, t) {
            const n = function() {
                !qA && "BroadcastChannel" in self && (qA = new BroadcastChannel("[Firebase] FID Change"), qA.onmessage = e => {
                    ZA(e.data.key, e.data.fid)
                });
                return qA
            }();
            n && n.postMessage({
                key: e,
                fid: t
            });
            0 === UA.size && qA && (qA.close(), qA = null)
        }(n, t)
}

function ZA(e, t) {
    const n = UA.get(e);
    if (n)
        for (const r of n) r(t)
}
let qA = null;
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
const $A = "firebase-installations-store";
let GA = null;

function KA() {
    return GA || (GA = wV("firebase-installations-database", 1, {
        upgrade: (e, t) => {
            if (0 === t) e.createObjectStore($A)
        }
    })), GA
}
async function QA(e, t) {
    const n = zA(e),
        r = (await KA()).transaction($A, "readwrite"),
        a = r.objectStore($A),
        i = await a.get(n);
    return await a.put(t, n), await r.done, i && i.fid === t.fid || WA(e, t.fid), t
}
async function JA(e) {
    const t = zA(e),
        n = (await KA()).transaction($A, "readwrite");
    await n.objectStore($A).delete(t), await n.done
}
async function XA(e, t) {
    const n = zA(e),
        r = (await KA()).transaction($A, "readwrite"),
        a = r.objectStore($A),
        i = await a.get(n),
        o = t(i);
    return void 0 === o ? await a.delete(n) : await a.put(o, n), await r.done, !o || i && i.fid === o.fid || WA(e, o.fid), o
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
async function eO(e) {
    let t;
    const n = await XA(e.appConfig, n => {
        const r = function(e) {
                const t = e || {