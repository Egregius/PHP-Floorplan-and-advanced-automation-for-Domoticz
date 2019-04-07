jQuery.fn.dataTableExt.afnFiltering.push(
    function( oSettings, aData, iDataIndex ) {
        var iColumn = 3;
        var iMin = document.getElementById('min').value * 1;
        var iMax = document.getElementById('max').value * 1;
 
        var iVersion = aData[iColumn] == "-" ? 0 : aData[iColumn]*1;
        if ( iMin === "" && iMax === "" )
        {
            return true;
        }
        else if ( iMin === "" && iVersion < iMax )
        {
            return true;
        }
        else if ( iMin < iVersion && "" === iMax )
        {
            return true;
        }
        else if ( iMin < iVersion && iVersion < iMax )
        {
            return true;
        }
        return false;
    }
);
