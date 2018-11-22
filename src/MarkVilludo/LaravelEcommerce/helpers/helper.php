<?php
use Illuminate\Pagination\LengthAwarePaginator;
use Intervention\Image\Facades\Image;

if (!function_exists('paginateCollection')) {
    /**
     * Help paginate a collection
     *
     * @param array/object (collection) $collections
     * @param int $perPage
     * @return object
     */
    function paginateCollection($collections, $perPage = 10)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = collect($collections);
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        return $entries;
    }
}

if (!function_exists('computeDiscount')) {
    /**
     * Help compute discount
     *
     * @param float $original_price
     * @param float $discount
     * @param boolean $is_fixed
     * @return array
     */
    function computeDiscount($original_price, $discount, $is_fixed)
    {
        if ($is_fixed == true) {
            $discount_on_decimal = round($original_price * ($discount/100), 2);
            
            $return['original_price'] = $original_price;
            $return['total'] = number_format($original_price - $discount_on_decimal, 2);
            $return['discount'] =  $discount_on_decimal;
            return $return;
        }

        $return['original_price'] = $original_price;
        $return['total'] = number_format($original_price - $discount, 2);
        $return['discount'] = $discount;
        return $return;
    }
}

if (!function_exists('resizeAndSave')) {
    /**
     * Help resize images regardless of image dimension then save
     *
     * @param file $file
     * @param int $size
     * @param string $filepath
     * @param string $filename
     * @return response
     */
    function resizeAndSave($file, $size, $filepath, $filename)
    {

        $image = Image::make($file);
        $height = $image->height();
        $width = $image->width();

        if (($height > $size && $width > $size) || $height > $size || $width > $size) {
            if ($height > $width) {
              //resize to width and constraint then crop to meet size
                return $image->resize($size, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path($filepath .'/'. $filename));
            } elseif ($height < $width) {
              //resize to height and contrain, crop, then save
                return $image->resize(null, $size, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path($filepath .'/'. $filename));
            } else {
                return $image->resize($size, $size)->save(public_path($filepath .'/'. $filename));
            }
        } else {
            return $image->save(public_path($filepath .'/'. $filename));
        }
    }
}

if (!function_exists('resizeCropSquareAndSave')) {
    /**
     * Help crop and resize images to 1x1 dimension(square) then save
     *
     * @param file $file
     * @param int $size
     * @param string $filepath
     * @param string $filename
     * @return response
     */
    function resizeCropSquareAndSave($file, $size, $filepath, $filename)
    {

        $image = Image::make($file);
        $height = $image->height();
        $width = $image->width();

        if (($height > $size && $width > $size) || $height > $size || $width > $size) {
            if ($height > $width) {
              //resize to width and constraint then crop to meet size
                return $image->resize($size, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->crop($size, $size)->save(public_path($filepath .'/'. $filename));
            } elseif ($height < $width) {
              //resize to height and contrain, crop, then save
                return $image->resize(null, $size, function ($constraint) {
                    $constraint->aspectRatio();
                })->crop($size, $size)->save(public_path($filepath .'/'. $filename));
            } else {
                return $image->resize($size, $size)->save(public_path($filepath .'/'. $filename));
            }
        } else {
            return $image->save(public_path($filepath .'/'. $filename));
        }
    }
}

if (!function_exists('saveOriginal')) {
    /**
     * Save images to folder
     *
     * @param file $file
     * @param string $filepath
     * @param string $filename
     * @return response
     */
    function saveOriginal($file, $filepath, $filename)
    {

        $image = Image::make($file);
        return $image->save(public_path($filepath .'/'. $filename));
    }
}
