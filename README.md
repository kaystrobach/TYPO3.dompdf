# TYPO3 Extension dompdf

ensure the extension folder is named __dompdf__.

Example usage:
```
{namespace pdf = Tx_Dompdf_ViewHelpers}

<pdf:PdfViewHelper filename="{filename}" basepath="{basepath}" redirect="TRUE">
  Testitest
</pdf:PdfViewHelper>
```

* filename defines the name of the output file (basename only)
* basepath is the base path for including images, css, etc.
* redirect is only usefull, if you want to provide the pdf as download, then a temp file will be created, cached and the user will be redirected to the related page
