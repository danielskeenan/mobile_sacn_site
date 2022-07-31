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
    const downloadButtonDropdownToggle = document.getElementById(
      'app-download-dropdown');
    const downloadButtonOthers = document.getElementById('app-download-others');
    if (downloadButton === null || downloadButtonMain === null || downloadButtonMainDesc === null
      || downloadButtonDropdownToggle === null || downloadButtonOthers === null) {
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
    let downloadButtonSet = false;
    if (osName !== undefined && BROWSER_OS_MAP.has(osName)) {
        const asset = Object.values(releaseManifest.assets)
          .find(a => a.platform === BROWSER_OS_MAP.get(osName));
        if (asset !== undefined) {
            downloadButtonMain.href = asset.url;
            downloadButtonMainDesc.innerText = `Download ${asset.kind} (${asset.platformTitle})`;
            downloadButtonSet = true;
        }
    }
    if (!downloadButtonSet) {
        // Remove the main download button and add its text to the dropdown toggler.
        const downloadText = downloadButtonMain.innerHTML;
        downloadButtonMain.remove();
        downloadButton.className = 'dropdown';
        downloadButtonDropdownToggle.classList.remove('dropdown-toggle-split');
        downloadButtonDropdownToggle.innerHTML = downloadText;
    }

    return;
});
