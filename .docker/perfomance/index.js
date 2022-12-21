import { check, sleep } from "k6";
import http from "k6/http";

export const options = {
    vus: __ENV.VIRTUALUSERS || 100,
    duration: `${__ENV.SECONDSDURATION || 5}s`,
};

export default function () {
    const response = http.get(__ENV.ENDPOINT, {
        headers: { Accepts: "application/json" },
    });
    check(response, { "status is 200": (r) => r.status === 200 });
    sleep(1);
}
