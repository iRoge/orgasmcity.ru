<?

namespace Qsoft\Helpers;

use CBitrixComponent;
use CCacheManager;
use CPHPCache;

class ComponentHelper extends CBitrixComponent
{
    /**
     * @var CCacheManager
     */
    protected $cacheManger;
    /**
     * @var CPHPCache
     */
    protected $cache;
    /**
     * @var Path
     */
    protected $relativePath;
    // input
    protected function tryParseInt(&$fld, $default = false, $allowZero = false): void
    {
        $fld = intval($fld);
        if (!$allowZero && !$fld && $default !== false) {
            $fld = $default;
        }
    }
    protected function tryParseString(&$fld, $default = false): void
    {
        $fld = trim((string)$fld);
        if (!strlen($fld) && $default !== false) {
            $fld = $default;
        }
        $fld = htmlspecialcharsbx($fld);
    }
    public function onPrepareComponentParams($arParams)
    {
        global $CACHE_MANAGER;
        $this->cacheManger = $CACHE_MANAGER;
        $this->cache = new CPHPCache();
    }
    // cache
    protected function initCache(string $type): bool
    {
        return $this->cache->InitCache($this->arParams['CACHE_TIME'], $this->buildCacheId($type), $this->getRelativePath() ?? $this->relativePath);
    }
    protected function buildCacheId(string $type)
    {
        $params = $this->arParams;
        $params[] = $type;

        return serialize($params);
    }
    protected function getCachedVars(string $key)
    {
        $cachedVars = $this->cache->GetVars();

        return $cachedVars[$key] ?? [];
    }
    protected function startCache(): bool
    {
        return $this->cache->StartDataCache();
    }
    protected function startTagCache(): void
    {
        $this->cacheManger->StartTagCache($this->getRelativePath() ?? $this->relativePath);
    }
    protected function registerTag(string $tag): void
    {
        $this->cacheManger->RegisterTag($tag);
    }
    protected function endTagCache(): void
    {
        $this->cacheManger->EndTagCache();
    }
    protected function saveToCache(string $key, $values): void
    {
        $this->cache->EndDataCache([$key => $values]);
    }
    protected function abortTagCache(): void
    {
        $this->cacheManger->AbortTagCache();
    }
    protected function abortCache(): void
    {
        $this->cache->AbortDataCache();
    }
    // time
    protected function getTime($type, $diff = false)
    {
        if (in_array($type, array("start", "end"))) {
            $format = "SHORT";
            if ($type == "end") {
                $diff = 86400;
            }
        } else {
            $format = "FULL";
        }
        $timestamp = MakeTimeStamp(ConvertTimeStamp(false, $format));
        if ($diff) {
            $timestamp += $diff;
        }
        return ConvertTimeStamp($timestamp, 'FULL');
    }
}
