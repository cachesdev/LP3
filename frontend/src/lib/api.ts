import ky from "ky";

export const apiUrl = "/api/v1"

export const api = ky.extend({prefixUrl: apiUrl, mode: "cors"})