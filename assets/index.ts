import './styles/index.scss';
import 'bootstrap';
import Bowser from "bowser";

interface ReleaseManifest
{
    title: string;
    version: string;
    published: string;
    channel: string;
    assets: Record<string, ReleaseAsset>;
}

interface ReleaseAsset
{
    filename: string;
    size: number;
    kind: string;
    platform: 'ubuntu' | 'macos' | 'windows';
    platformTitle: string;
    dsa: string;
    url: string;
}

const BROWSER_OS_MAP = new Map([
    ['Linux', 'ubuntu'],
    ['macOS', 'macos'],
    ['Windows', 'windows'],
]);

window.addEventListener('DOMContentLoaded', () => {
    // Find Download button and set its href attribute to the appropriate target based on the useragent.
    const bowser = Bowser.getParser(window.navigator.userAgent);
    const downloadButton = document.getElementById('app-download');
    const downloadButtonMain = document.getElementById('app-download-main') as HTMLAnchorElement | null;
    const downloadButtonMainDesc = document.getElementById(
      'app-download-main-desc');
    if (downloadButton === null || downloadButtonMain === null || downloadButtonMainDesc === null) {
        console.error('Download button not in DOM.');
        return;
    }
    if (downloadButton.dataset['releaseManifest'] === undefined) {
        console.error('Release manifest not in DOM.');
        return;
    }
    const releaseManifest = JSON.parse(downloadButton.dataset['releaseManifest']) as ReleaseManifest;

    // Decide which OS we're on.
    const osName = bowser.getOS().name;
    if (osName !== undefined && BROWSER_OS_MAP.has(osName)) {
        const asset = Object.values(releaseManifest.assets)
          .find(a => a.platform === BROWSER_OS_MAP.get(osName));
        if (asset !== undefined) {
            downloadButtonMain.href = asset.url;
            downloadButtonMainDesc.innerText = `Download ${asset.kind} (${asset.platformTitle})`;
        }
    }

    return;
});
