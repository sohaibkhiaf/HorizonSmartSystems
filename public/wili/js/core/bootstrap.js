const byId = (id) => document.getElementById(id);

const gateRoot = byId("gateRoot");
const appRoot = byId("appRoot");
const stepActivate = byId("stepActivate");
const stepBootstrap = byId("stepBootstrap");
const stepLogin = byId("stepLogin");

const activateForm = byId("activateForm");
const bootstrapForm = byId("bootstrapForm");
const loginForm = byId("loginForm");

const activateMsg = byId("activateMsg");
const bootstrapMsg = byId("bootstrapMsg");
const loginMsg = byId("loginMsg");

const headerTitle = byId("headerTitle");
const headerSub = byId("headerSub");
const userBadge = byId("userBadge");

const views = ["dashboard", "caisse", "alerts", "sales", "salesHistory", "customerDebt", "customerJournal", "customerReturn", "products", "history", "losses", "customers", "suppliers", "supplierJournal", "supplierReturn", "supplierDebt", "purchases", "purchasesJournal", "finance", "reports", "audit", "backups", "priceDisplay", "settings"];

const ui = {
  subnav: byId("subnav"),
  sbUser: byId("sbUser"),
  sbView: byId("sbView"),
  sbClock: byId("sbClock"),
  notifBtn: byId("notifBtn"),
  notifBadge: byId("notifBadge"),
  alertsPanel: byId("alertsPanel"),
  alertsList: byId("alertsList"),
  alertsRefreshBtn: byId("alertsRefreshBtn"),

  kpiSales: byId("kpiSales"),
  kpiPurchases: byId("kpiPurchases"),
  kpiProfit: byId("kpiProfit"),
  kpiCash: byId("kpiCash"),
  dashSummary: byId("dashSummary"),
  dashFinance: byId("dashFinance"),
  dashboardCharts: byId("dashboardCharts"),

  productForm: byId("productForm"),
  productId: byId("productId"),
  productName: byId("productName"),
  productType: byId("productType"),
  productImagePath: byId("productImagePath"),
  productImagePickBtn: byId("productImagePickBtn"),
  productImagePreview: byId("productImagePreview"),
  productBarcode: byId("productBarcode"),
  productBarcodeGenerateBtn: byId("productBarcodeGenerateBtn"),
  productReference: byId("productReference"),
  productCost: byId("productCost"),
  productCostMeta: byId("productCostMeta"),
  productWholesale: byId("productWholesale"),
  productRetail: byId("productRetail"),
  productAssist: byId("productAssist"),
  productMarginHint: byId("productMarginHint"),
  productStock: byId("productStock"),
  productExpiry: byId("productExpiry"),
  productSubmitBtn: byId("productSubmitBtn"),
  productCancelBtn: byId("productCancelBtn"),
  productAddBtn: byId("productAddBtn"),
  productModal: byId("productModal"),
  productModalTitle: byId("productModalTitle"),
  productModalCloseBtn: byId("productModalCloseBtn"),
  productGridSearch: byId("productGridSearch"),
  productGridMeta: byId("productGridMeta"),
  productsTable: byId("productsTable"),
  productsTableBody: byId("productsTableBody"),
  productsPagePrevBtn: byId("productsPagePrevBtn"),
  productsPageInfo: byId("productsPageInfo"),
  productsPageNextBtn: byId("productsPageNextBtn"),

  historyForm: byId("historyForm"),
  historyProduct: byId("historyProduct"),
  historyProductSearch: byId("historyProductSearch"),
  historyProductSuggest: byId("historyProductSuggest"),
  historyStatsForm: byId("historyStatsForm"),
  historyStatsFrom: byId("historyStatsFrom"),
  historyStatsTo: byId("historyStatsTo"),
  historyStatsCompare: byId("historyStatsCompare"),
  historyFilterForm: byId("historyFilterForm"),
  historyType: byId("historyType"),
  historyFrom: byId("historyFrom"),
  historyTo: byId("historyTo"),
  historyRef: byId("historyRef"),
  historyUser: byId("historyUser"),
  historyTableBody: byId("historyTableBody"),
  historyBox: byId("historyBox"),
  historyCharts: byId("historyCharts"),
  stockLossForm: byId("stockLossForm"),
  lossProduct: byId("lossProduct"),
  lossProductSearch: byId("lossProductSearch"),
  lossProductSuggest: byId("lossProductSuggest"),
  lossQty: byId("lossQty"),
  lossReason: byId("lossReason"),
  lossNote: byId("lossNote"),
  lossSubmitBtn: byId("lossSubmitBtn"),
  lossFilterSearch: byId("lossFilterSearch"),
  lossFilterReason: byId("lossFilterReason"),
  lossFilterFrom: byId("lossFilterFrom"),
  lossFilterTo: byId("lossFilterTo"),
  lossFilterClear: byId("lossFilterClear"),
  lossCount: byId("lossCount"),
  lossUnits: byId("lossUnits"),
  lossTotalCost: byId("lossTotalCost"),
  lossTable: byId("lossTable"),
  lossTableBody: byId("lossTableBody"),

  customerForm: byId("customerForm"),
  customerId: byId("customerId"),
  customerName: byId("customerName"),
  customerPhone: byId("customerPhone"),
  customerNif: byId("customerNif"),
  customerNis: byId("customerNis"),
  customerRc: byId("customerRc"),
  customerAi: byId("customerAi"),
  customerAddress: byId("customerAddress"),
  customerActivity: byId("customerActivity"),
  customerCommerceName: byId("customerCommerceName"),
  customerSubmitBtn: byId("customerSubmitBtn"),
  customerCancelBtn: byId("customerCancelBtn"),
  customerAddBtn: byId("customerAddBtn"),
  customerModal: byId("customerModal"),
  customerModalTitle: byId("customerModalTitle"),
  customerModalCloseBtn: byId("customerModalCloseBtn"),
  customerGridSearch: byId("customerGridSearch"),
  customerGridSort: byId("customerGridSort"),
  customerGridMeta: byId("customerGridMeta"),
  customersTableWrap: byId("customersTableWrap"),
  customersTable: byId("customersTable"),
  customersTableBody: byId("customersTableBody"),
  customersPagePrevBtn: byId("customersPagePrevBtn"),
  customersPageInfo: byId("customersPageInfo"),
  customersPageNextBtn: byId("customersPageNextBtn"),
  customersPdfBtn: byId("customersPdfBtn"),
  customerTraceForm: byId("customerTraceForm"),
  customerTraceCustomer: byId("customerTraceCustomer"),
  customerTraceCustomerSearch: byId("customerTraceCustomerSearch"),
  customerTraceCustomerSuggest: byId("customerTraceCustomerSuggest"),
  customerTraceFrom: byId("customerTraceFrom"),
  customerTraceTo: byId("customerTraceTo"),
  customerTraceType: byId("customerTraceType"),
  customerTraceDebt: byId("customerTraceDebt"),
  customerTraceBox: byId("customerTraceBox"),
  customerReturnForm: byId("customerReturnForm"),
  customerReturnCustomer: byId("customerReturnCustomer"),
  customerReturnCustomerSearch: byId("customerReturnCustomerSearch"),
  customerReturnCustomerSuggest: byId("customerReturnCustomerSuggest"),
  customerReturnSale: byId("customerReturnSale"),
  customerReturnProduct: byId("customerReturnProduct"),
  customerReturnProductSearch: byId("customerReturnProductSearch"),
  customerReturnProductSuggest: byId("customerReturnProductSuggest"),
  customerReturnQty: byId("customerReturnQty"),
  customerReturnPrice: byId("customerReturnPrice"),
  customerReturnNote: byId("customerReturnNote"),
  customerReturnAddLineBtn: byId("customerReturnAddLineBtn"),
  customerReturnSubmitBtn: byId("customerReturnSubmitBtn"),
  customerReturnClearBtn: byId("customerReturnClearBtn"),
  customerReturnLinesBody: byId("customerReturnLinesBody"),
  customerReturnTotal: byId("customerReturnTotal"),
  customerReturnHint: byId("customerReturnHint"),

  supplierForm: byId("supplierForm"),
  supplierId: byId("supplierId"),
  supplierName: byId("supplierName"),
  supplierPhone: byId("supplierPhone"),
  supplierNif: byId("supplierNif"),
  supplierNis: byId("supplierNis"),
  supplierRc: byId("supplierRc"),
  supplierAi: byId("supplierAi"),
  supplierAddress: byId("supplierAddress"),
  supplierActivity: byId("supplierActivity"),
  supplierCommerceName: byId("supplierCommerceName"),
  supplierSubmitBtn: byId("supplierSubmitBtn"),
  supplierCancelBtn: byId("supplierCancelBtn"),
  supplierAddBtn: byId("supplierAddBtn"),
  supplierModal: byId("supplierModal"),
  supplierModalTitle: byId("supplierModalTitle"),
  supplierModalCloseBtn: byId("supplierModalCloseBtn"),
  supplierGridSearch: byId("supplierGridSearch"),
  supplierGridSort: byId("supplierGridSort"),
  supplierGridMeta: byId("supplierGridMeta"),
  suppliersTableWrap: byId("suppliersTableWrap"),
  suppliersTable: byId("suppliersTable"),
  suppliersTableBody: byId("suppliersTableBody"),
  suppliersPagePrevBtn: byId("suppliersPagePrevBtn"),
  suppliersPageInfo: byId("suppliersPageInfo"),
  suppliersPageNextBtn: byId("suppliersPageNextBtn"),
  suppliersPdfBtn: byId("suppliersPdfBtn"),
  supplierOpenFcBtn: byId("supplierOpenFcBtn"),
  supplierTraceForm: byId("supplierTraceForm"),
  supplierTraceSupplier: byId("supplierTraceSupplier"),
  supplierTraceSupplierSearch: byId("supplierTraceSupplierSearch"),
  supplierTraceSupplierSuggest: byId("supplierTraceSupplierSuggest"),
  supplierTraceFrom: byId("supplierTraceFrom"),
  supplierTraceTo: byId("supplierTraceTo"),
  supplierTraceType: byId("supplierTraceType"),
  supplierTraceDebt: byId("supplierTraceDebt"),
  supplierTraceBox: byId("supplierTraceBox"),
  supplierReturnForm: byId("supplierReturnForm"),
  supplierReturnSupplier: byId("supplierReturnSupplier"),
  supplierReturnSupplierSearch: byId("supplierReturnSupplierSearch"),
  supplierReturnSupplierSuggest: byId("supplierReturnSupplierSuggest"),
  supplierReturnPurchase: byId("supplierReturnPurchase"),
  supplierReturnProductSearch: byId("supplierReturnProductSearch"),
  supplierReturnProductSuggest: byId("supplierReturnProductSuggest"),
  supplierReturnProduct: byId("supplierReturnProduct"),
  supplierReturnQty: byId("supplierReturnQty"),
  supplierReturnCost: byId("supplierReturnCost"),
  supplierReturnNote: byId("supplierReturnNote"),
  supplierReturnAddLineBtn: byId("supplierReturnAddLineBtn"),
  supplierReturnSubmitBtn: byId("supplierReturnSubmitBtn"),
  supplierReturnClearBtn: byId("supplierReturnClearBtn"),
  supplierReturnLinesBody: byId("supplierReturnLinesBody"),
  supplierReturnTotal: byId("supplierReturnTotal"),
  supplierReturnHint: byId("supplierReturnHint"),

  posSearchForm: byId("posSearchForm"),
  posSearchInput: byId("posSearchInput"),
  salePriceModes: Array.from(document.querySelectorAll('input[name="salePriceMode"]')),
  posQuickQty: byId("posQuickQty"),
  posLiveName: byId("posLiveName"),
  posLiveMeta: byId("posLiveMeta"),
  posLiveTotal: byId("posLiveTotal"),
  salesRuntimeMsg: byId("salesRuntimeMsg"),
  posResults: byId("posResults"),
  posCartList: byId("posCartList"),
  posCartTotals: byId("posCartTotals"),
  posGrandTotal: byId("posGrandTotal"),
  saleCheckoutForm: byId("saleCheckoutForm"),
  saleCustomer: byId("saleCustomer"),
  saleCustomerSearch: byId("saleCustomerSearch"),
  saleCustomerSuggest: byId("saleCustomerSuggest"),
  saleCustomerDebt: byId("saleCustomerDebt"),
  saleCreditCheck: byId("saleCreditCheck"),
  saleDiscountLabel: byId("saleDiscountLabel"),
  saleDiscountType: byId("saleDiscountType"),
  saleDiscountValue: byId("saleDiscountValue"),
  saleDiscountReset: byId("saleDiscountReset"),
  saleDiscountHint: byId("saleDiscountHint"),
  salePaid: byId("salePaid"),
  saleDebtForm: byId("saleDebtForm"),
  saleDebtCustomer: byId("saleDebtCustomer"),
  saleDebtCustomerSearch: byId("saleDebtCustomerSearch"),
  saleDebtCustomerSuggest: byId("saleDebtCustomerSuggest"),
  saleDebtInvoice: byId("saleDebtInvoice"),
  saleDebtAmount: byId("saleDebtAmount"),
  saleDebtNote: byId("saleDebtNote"),
  saleDebtPayBtn: byId("saleDebtPayBtn"),
  saleDebtTable: byId("saleDebtTable"),
  saleDebtTableBody: byId("saleDebtTableBody"),
  saleDebtMsg: byId("saleDebtMsg"),
  posValidateBtn: byId("posValidateBtn"),
  posClearBtn: byId("posClearBtn"),
  posClearBtn2: byId("posClearBtn2"),
  posPrintCartBtn: byId("posPrintCartBtn"),
  salesHistSearch: byId("salesHistSearch"),
  salesHistFrom: byId("salesHistFrom"),
  salesHistTo: byId("salesHistTo"),
  salesHistSort: byId("salesHistSort"),
  salesHistPageSize: byId("salesHistPageSize"),
  salesHistApplyBtn: byId("salesHistApplyBtn"),
  salesHistPdfBtn: byId("salesHistPdfBtn"),
  salesHistStats: byId("salesHistStats"),
  salesHistPrevBtn: byId("salesHistPrevBtn"),
  salesHistNextBtn: byId("salesHistNextBtn"),
  salesHistPageInfo: byId("salesHistPageInfo"),
  salesHistPrintSelectedBtn: byId("salesHistPrintSelectedBtn"),
  salesHistDetail: byId("salesHistDetail"),
  salesHistoryCharts: byId("salesHistoryCharts"),
  salesKpiCount: byId("salesKpiCount"),
  salesKpiTotal: byId("salesKpiTotal"),
  salesKpiDue: byId("salesKpiDue"),
  salesHistoryTable: byId("salesHistoryTable"),
  salesHistoryTableBody: byId("salesHistoryTableBody"),
  salesList: byId("salesList"),

  purchaseForm: byId("purchaseForm"),
  purchaseSupplier: byId("purchaseSupplier"),
  purchaseSupplierSearch: byId("purchaseSupplierSearch"),
  purchaseSupplierSuggest: byId("purchaseSupplierSuggest"),
  purchaseProduct: byId("purchaseProduct"),
  purchaseProductSearch: byId("purchaseProductSearch"),
  purchaseProductOptions: byId("purchaseProductOptions"),
  purchaseProductSuggest: byId("purchaseProductSuggest"),
  purchaseQty: byId("purchaseQty"),
  purchaseAddLineBtn: byId("purchaseAddLineBtn"),
  purchaseInlineToggleBtn: byId("purchaseInlineToggleBtn"),
  purchaseInlineCreate: byId("purchaseInlineCreate"),
  purchaseInlineCreateForm: byId("purchaseInlineCreateForm"),
  purchaseNewName: byId("purchaseNewName"),
  purchaseNewBarcode: byId("purchaseNewBarcode"),
  purchaseNewReference: byId("purchaseNewReference"),
  purchaseNewCost: byId("purchaseNewCost"),
  purchaseNewWholesale: byId("purchaseNewWholesale"),
  purchaseNewRetail: byId("purchaseNewRetail"),
  purchaseNewAssist: byId("purchaseNewAssist"),
  purchaseClearBtn: byId("purchaseClearBtn"),
  purchaseCartList: byId("purchaseCartList"),
  purchasePaid: byId("purchasePaid"),
  purchasePaidAutoFull: byId("purchasePaidAutoFull"),
  purchaseComputed: byId("purchaseComputed"),
  purchaseGrandTotal: byId("purchaseGrandTotal"),
  purchaseDueLabel: byId("purchaseDueLabel"),
  purchaseDueTotal: byId("purchaseDueTotal"),
  purchaseSaveBtn: byId("purchaseSaveBtn"),
  purchasePdfFcBtn: byId("purchasePdfFcBtn"),
  purchaseDebtForm: byId("purchaseDebtForm"),
  purchaseDebtSupplier: byId("purchaseDebtSupplier"),
  purchaseDebtSupplierSearch: byId("purchaseDebtSupplierSearch"),
  purchaseDebtSupplierSuggest: byId("purchaseDebtSupplierSuggest"),
  purchaseDebtInvoice: byId("purchaseDebtInvoice"),
  purchaseDebtAmount: byId("purchaseDebtAmount"),
  purchaseDebtNote: byId("purchaseDebtNote"),
  purchaseDebtPayBtn: byId("purchaseDebtPayBtn"),
  purchaseDebtTable: byId("purchaseDebtTable"),
  purchaseDebtTableBody: byId("purchaseDebtTableBody"),
  purchaseDebtMsg: byId("purchaseDebtMsg"),
  purchaseDetailBox: byId("purchaseDetailBox"),
  purchaseKpiCount: byId("purchaseKpiCount"),
  purchaseKpiTotal: byId("purchaseKpiTotal"),
  purchaseKpiDue: byId("purchaseKpiDue"),
  purchasesCharts: byId("purchasesCharts"),
  purchasesHistSearch: byId("purchasesHistSearch"),
  purchasesHistFrom: byId("purchasesHistFrom"),
  purchasesHistTo: byId("purchasesHistTo"),
  purchasesHistSort: byId("purchasesHistSort"),
  purchasesHistPageSize: byId("purchasesHistPageSize"),
  purchasesHistApplyBtn: byId("purchasesHistApplyBtn"),
  purchasesHistPrevBtn: byId("purchasesHistPrevBtn"),
  purchasesHistNextBtn: byId("purchasesHistNextBtn"),
  purchasesHistPageInfo: byId("purchasesHistPageInfo"),
  purchasesHistPrintSelectedBtn: byId("purchasesHistPrintSelectedBtn"),
  purchasesHistPdfBtn: byId("purchasesHistPdfBtn"),
  purchasesJournalTable: byId("purchasesJournalTable"),
  purchasesJournalTableBody: byId("purchasesJournalTableBody"),
  purchasesList: byId("purchasesList"),

  financeForm: byId("financeForm"),
  financeType: byId("financeType"),
  financeAmount: byId("financeAmount"),
  financeNote: byId("financeNote"),
  financeEntryPartyMode: byId("financeEntryPartyMode"),
  financeEntryParty: byId("financeEntryParty"),
  financeCharts: byId("financeCharts"),
  financeAnalysisForm: byId("financeAnalysisForm"),
  financeAnalysisFrom: byId("financeAnalysisFrom"),
  financeAnalysisTo: byId("financeAnalysisTo"),
  financeAnalysisApplyBtn: byId("financeAnalysisApplyBtn"),
  financeFilterForm: byId("financeFilterForm"),
  financeParty: byId("financeParty"),
  financeDay: byId("financeDay"),
  financeFrom: byId("financeFrom"),
  financeTo: byId("financeTo"),
  financeSearch: byId("financeSearch"),
  financePageSize: byId("financePageSize"),
  financeApplyBtn: byId("financeApplyBtn"),
  financeSummary: byId("financeSummary"),
  financeTable: byId("financeTable"),
  financeTableBody: byId("financeTableBody"),
  financeList: byId("financeList"),
  financePrevBtn: byId("financePrevBtn"),
  financeNextBtn: byId("financeNextBtn"),
  financePageInfo: byId("financePageInfo"),
  financePageNums: byId("financePageNums"),

  reportForm: byId("reportForm"),
  reportDate: byId("reportDate"),
  reportPdfBtn: byId("reportPdfBtn"),
  reportExportBox: byId("reportExportBox"),
  reportBox: byId("reportBox"),
  reportDebtCustomersPdfBtn: byId("reportDebtCustomersPdfBtn"),
  reportDebtSuppliersPdfBtn: byId("reportDebtSuppliersPdfBtn"),
  debtKpiCustomers: byId("debtKpiCustomers"),
  debtKpiSuppliers: byId("debtKpiSuppliers"),
  debtKpiNet: byId("debtKpiNet"),
  debtCharts: byId("debtCharts"),
  debtSummaryBox: byId("debtSummaryBox"),
  auditForm: byId("auditForm"),
  auditSearch: byId("auditSearch"),
  auditAction: byId("auditAction"),
  auditFrom: byId("auditFrom"),
  auditTo: byId("auditTo"),
  auditLoadBtn: byId("auditLoadBtn"),
  auditPdfBtn: byId("auditPdfBtn"),
  auditStats: byId("auditStats"),
  auditList: byId("auditList"),
  auditPrevBtn: byId("auditPrevBtn"),
  auditNextBtn: byId("auditNextBtn"),
  auditPageInfo: byId("auditPageInfo"),
  auditPageNums: byId("auditPageNums"),
  pdfPreviewModal: byId("pdfPreviewModal"),
  pdfPreviewTitle: byId("pdfPreviewTitle"),
  pdfPreviewFrame: byId("pdfPreviewFrame"),
  pdfPreviewCloseBtn: byId("pdfPreviewCloseBtn"),
  pdfEditBtn: byId("pdfEditBtn"),
  pdfResetBtn: byId("pdfResetBtn"),
  pdfPrintBtn: byId("pdfPrintBtn"),
  pdfSaveAsBtn: byId("pdfSaveAsBtn"),
  journalDetailModal: byId("journalDetailModal"),
  journalDetailTitle: byId("journalDetailTitle"),
  journalDetailBody: byId("journalDetailBody"),
  journalDetailPdfBtn: byId("journalDetailPdfBtn"),
  journalDetailCloseBtn: byId("journalDetailCloseBtn"),
  confirmModal: byId("confirmModal"),
  confirmTitle: byId("confirmTitle"),
  confirmMessage: byId("confirmMessage"),
  confirmOkBtn: byId("confirmOkBtn"),
  confirmCancelBtn: byId("confirmCancelBtn"),
  returnModal: byId("returnModal"),
  returnTitle: byId("returnTitle"),
  returnQtyInput: byId("returnQtyInput"),
  returnNoteInput: byId("returnNoteInput"),
  returnOkBtn: byId("returnOkBtn"),
  returnCancelBtn: byId("returnCancelBtn"),
  userPinModal: byId("userPinModal"),
  userPinTitle: byId("userPinTitle"),
  userPinValue: byId("userPinValue"),
  userPinConfirm: byId("userPinConfirm"),
  userPinOkBtn: byId("userPinOkBtn"),
  userPinCancelBtn: byId("userPinCancelBtn"),
  barcodeSizeModal: byId("barcodeSizeModal"),
  barcodeSizeTitle: byId("barcodeSizeTitle"),
  barcodeSizeMessage: byId("barcodeSizeMessage"),
  barcodeSizeSmallBtn: byId("barcodeSizeSmallBtn"),
  barcodeSizeMediumBtn: byId("barcodeSizeMediumBtn"),
  barcodeSizeLargeBtn: byId("barcodeSizeLargeBtn"),
  barcodeSizeCancelBtn: byId("barcodeSizeCancelBtn"),
  appToastStack: byId("appToastStack"),

  settingsForm: byId("settingsForm"),
  settingsSaveMsg: byId("settingsSaveMsg"),
  langForm: byId("langForm"),
  setAllowNegativeStock: byId("setAllowNegativeStock"),
  setAutoSettleDocuments: byId("setAutoSettleDocuments"),
  setAutoPrintSaleCart: byId("setAutoPrintSaleCart"),
  printProfilesGrid: byId("printProfilesGrid"),
  printPrintersRefreshBtn: byId("printPrintersRefreshBtn"),
  printPrinterStatus: byId("printPrinterStatus"),
  setLanguage: byId("setLanguage"),
  setCurrency: byId("setCurrency"),
  setUiFontSize: byId("setUiFontSize"),
  setUiDisplayProfile: byId("setUiDisplayProfile"),
  setUiFontBold: byId("setUiFontBold"),
  setInvoiceTaxRate: byId("setInvoiceTaxRate"),
  setProductImagesDir: byId("setProductImagesDir"),
  productImagesDirPickBtn: byId("productImagesDirPickBtn"),
  productImagesDirDefaultBtn: byId("productImagesDirDefaultBtn"),
  productImagesDirHint: byId("productImagesDirHint"),
  setComptoirLayout: byId("setComptoirLayout"),
  comptoirLayoutPreview: byId("comptoirLayoutPreview"),
  setBarcodeShowName: byId("setBarcodeShowName"),
  setBarcodeShowRef: byId("setBarcodeShowRef"),
  setBarcodeShowPrice: byId("setBarcodeShowPrice"),
  setStoreName: byId("setStoreName"),
  setStoreActivity: byId("setStoreActivity"),
  setStoreAddress: byId("setStoreAddress"),
  setStorePhone: byId("setStorePhone"),
  setStoreEmail: byId("setStoreEmail"),
  setStoreNif: byId("setStoreNif"),
  setStoreNis: byId("setStoreNis"),
  setStoreRc: byId("setStoreRc"),
  setStoreAi: byId("setStoreAi"),
  backupCreateBtn: byId("backupCreateBtn"),
  backupImportBtn: byId("backupImportBtn"),
  backupScope: byId("backupScope"),
  backupBox: byId("backupBox"),
  priceDisplayForm: byId("priceDisplayForm"),
  priceDisplayMonitor: byId("priceDisplayMonitor"),
  priceDisplayMode: byId("priceDisplayMode"),
  priceDisplayCount: byId("priceDisplayCount"),
  priceDisplayScannerBaud: byId("priceDisplayScannerBaud"),
  priceDisplayScannerDetectBtn: byId("priceDisplayScannerDetectBtn"),
  priceDisplayScanners: byId("priceDisplayScanners"),
  priceDisplaySerialStatus: byId("priceDisplaySerialStatus"),
  priceDisplayOpenBtn: byId("priceDisplayOpenBtn"),
  priceDisplayRefreshBtn: byId("priceDisplayRefreshBtn"),
  priceDisplayCloseAllBtn: byId("priceDisplayCloseAllBtn"),
  priceDisplayMsg: byId("priceDisplayMsg"),
  priceDisplayWindows: byId("priceDisplayWindows"),
  userForm: byId("userForm"),
  userName: byId("userName"),
  userDisplayName: byId("userDisplayName"),
  userRole: byId("userRole"),
  userPin: byId("userPin"),
  userSaveMsg: byId("userSaveMsg"),
  usersList: byId("usersList")
};

const session = { user: null, language: "fr" };
const cache = { products: [], customers: [], suppliers: [], settings: {} };
const posCart = [];
let editingSaleId = "";
let editingSaleRef = "";
let lastPurchaseForPdf = null;
const openTabs = ["dashboard"];
let searchRows = [];
let searchSelectedIndex = 0;
const posSearchSortState = { key: "name", dir: "asc" };
let salesRows = [];
let salesRowsFiltered = [];
let salesHistPage = 1;
let salesSelectedId = "";
const salesJournalState = { sortKey: "created_at", sortDir: "desc" };
let saleSubmitInFlight = false;
let salesPublicRefMap = new Map();
const salesDetailsCache = new Map();
let salesDetailLoadSeq = 0;
let purchaseRows = [];
let purchaseRowsFiltered = [];
let purchaseSelectedId = "";
let purchasesHistPage = 1;
const purchasesJournalState = { sortKey: "created_at", sortDir: "desc" };
let purchasesPublicRefMap = new Map();
const purchaseDetailsCache = new Map();
const purchaseCart = [];
let editingPurchaseId = "";
let editingPurchaseRef = "";
let purchasePaidManual = false;
let purchasePaidAutoFull = false;
let financeRows = [];
let financeRowsFiltered = [];
let financePage = 1;
const financeJournalState = { sortKey: "created_at", sortDir: "desc" };
let auditRows = [];
let auditPage = 1;
const AUDIT_PAGE_SIZE = 20;
let debtSummaryState = null;
let historyTraceState = null;
let lastPdfPath = "";
let lastPdfName = "";
let lastPdfEditableHtml = "";
let pdfEditMode = false;
const productGridState = {
  rows: [],
  filtered: [],
  sortKey: "name",
  sortDir: "asc",
  selectedIndex: -1,
  page: 1,
  pageSize: 100,
  totalRows: 0,
  totalStock: 0,
  totalPages: 1,
  searchMode: false
};
const customerGridState = {
  rows: [],
  filtered: [],
  sortKey: "name",
  sortDir: "asc",
  selectedIndex: -1,
  page: 1,
  pageSize: 100,
  totalRows: 0,
  totalPages: 1,
  searchMode: false
};
const supplierGridState = {
  rows: [],
  filtered: [],
  sortKey: "name",
  sortDir: "asc",
  selectedIndex: -1,
  page: 1,
  pageSize: 100,
  totalRows: 0,
  totalPages: 1,
  searchMode: false
};
let customerTraceRows = [];
let supplierTraceRows = [];
let currentAlerts = [];
let activeDomain = "comptoir";
let activeView = "dashboard";
const SALE_SUBMIT_TIMEOUT_MS = 20000;
let saleSubmitWatchdog = null;
let salesPostCommitRefreshInFlight = false;
let journalDetailContext = { type: "", id: "" };
const DEBUG_UI_RING_MAX = 160;
const debugUiRing = [];
const debugReportThrottle = new Map();

const UI_FONT_SCALE_TO_SIZE = {
  small: "13px",
  medium: "14px",
  large: "16px",
  xlarge: "18px"
};
const UI_FONT_SCALE_TO_ZOOM = {
  small: "0.95",
  medium: "1",
  large: "1.08",
  xlarge: "1.16"
};

const DOMAIN_CONFIG = {
  comptoir: {
    label: "Ventes",
    defaultView: "sales",
    views: ["sales", "salesHistory", "purchasesJournal"]
  },
  stock: {
    label: "Stock",
    defaultView: "products",
    views: ["products", "history", "losses"]
  },
  customers: {
    label: "Clients",
    defaultView: "customers",
    views: ["customers", "customerJournal", "customerReturn", "customerDebt"]
  },
  suppliers: {
    label: "Fournisseurs",
    defaultView: "suppliers",
    views: ["suppliers", "supplierJournal", "supplierReturn", "purchases", "supplierDebt"]
  },
  settings: {
    label: "Parametres",
    defaultView: "dashboard",
    views: ["dashboard", "caisse", "alerts", "finance", "reports", "backups", "audit", "priceDisplay", "settings"]
  }
};

const USER_ROLES = ["seller", "caisse_only", "stock_only", "finance_read_only", "admin"];
const ROLE_VIEW_ACCESS = {
  admin: new Set(views),
  seller: new Set(["dashboard", "sales", "salesHistory", "customerJournal", "customerReturn", "products", "history", "losses", "customers", "suppliers", "supplierJournal", "supplierReturn", "purchases", "purchasesJournal", "reports"]),
  caisse_only: new Set(["dashboard", "sales", "salesHistory", "customerJournal", "customerReturn", "customers"]),
  stock_only: new Set(["products", "history", "losses", "suppliers", "supplierJournal", "supplierReturn", "purchases", "purchasesJournal"]),
  finance_read_only: new Set(["dashboard", "caisse", "alerts", "salesHistory", "customerDebt", "customerJournal", "suppliers", "supplierJournal", "supplierDebt", "purchasesJournal", "finance", "reports", "audit"])
};
const ROLE_PERMISSIONS = {
  admin: new Set(["*"]),
  seller: new Set(["sales.read", "sales.write", "customers.read", "customers.write", "suppliers.read", "suppliers.write", "purchases.read", "purchases.write", "inventory.read", "inventory.write", "reports.read"]),
  caisse_only: new Set(["inventory.read", "sales.read", "sales.write", "customers.read", "customers.write"]),
  stock_only: new Set(["inventory.read", "inventory.write", "suppliers.read", "suppliers.write", "purchases.read", "purchases.write"]),
  finance_read_only: new Set(["sales.read", "purchases.read", "customers.read", "suppliers.read", "finance.read", "reports.read", "audit.read"])
};

function normalizeUserRole(role, fallback = "seller") {
  const raw = String(role || "").trim().toLowerCase();
  if (USER_ROLES.includes(raw)) return raw;
  return USER_ROLES.includes(String(fallback || "").trim().toLowerCase()) ? String(fallback).trim().toLowerCase() : "seller";
}

function isAdminUser() {
  return Boolean(session.user) && normalizeUserRole(session.user.role) === "admin";
}

function hasPermission(permission) {
  if (!session.user) return true;
  const wanted = String(permission || "").trim();
  if (!wanted) return true;
  const role = normalizeUserRole(session.user.role);
  const allowed = ROLE_PERMISSIONS[role] || ROLE_PERMISSIONS.seller;
  return allowed.has("*") || allowed.has(wanted);
}

function getUserRoleLabel(roleValue) {
  const role = normalizeUserRole(roleValue);
  if (role === "admin") return langText("Admin", "مدير", "ادمين");
  if (role === "caisse_only") return langText("Caisse only", "الصندوق فقط", "لاكاص برك");
  if (role === "stock_only") return langText("Stock only", "المخزون فقط", "الستوك برك");
  if (role === "finance_read_only") return langText("Finance lecture", "مالية قراءة فقط", "الفينونس غير قراية");
  return langText("Vendeur", "بائع", "ڤندور");
}

function userRoleOptionsHtml(selectedRole, extra = {}) {
  const current = normalizeUserRole(selectedRole);
  const disabledAdmin = Boolean(extra && extra.disableAdmin);
  return USER_ROLES.map((role) => {
    const selected = role === current ? "selected" : "";
    const disabled = disabledAdmin && role !== "admin" ? "disabled" : "";
    return `<option value="${role}" ${selected} ${disabled}>${esc(getUserRoleLabel(role))}</option>`;
  }).join("");
}

function populateUserRoleSelect(selectEl, selectedRole = "seller", extra = {}) {
  if (!selectEl) return;
  selectEl.innerHTML = userRoleOptionsHtml(selectedRole, extra);
  selectEl.value = normalizeUserRole(selectedRole);
}

function canAccessDomain(domain) {
  if (!session.user) return true;
  const cfg = DOMAIN_CONFIG[String(domain || "")] || DOMAIN_CONFIG.comptoir;
  return (cfg.views || []).some((view) => canAccessView(view));
}

function canAccessView(view) {
  if (!session.user) return true;
  const role = normalizeUserRole(session.user.role);
  const allowed = ROLE_VIEW_ACCESS[role] || ROLE_VIEW_ACCESS.seller;
  return allowed.has(String(view || ""));
}

function firstAccessibleViewForDomain(domain) {
  const cfg = DOMAIN_CONFIG[String(domain || "")] || DOMAIN_CONFIG.comptoir;
  const allowed = (cfg.views || []).filter((v) => canAccessView(v));
  if (allowed.length) return allowed[0];
  return canAccessView("sales") ? "sales" : (views.find((v) => canAccessView(v)) || "sales");
}

function normalizeTargetView(view) {
  const wanted = views.includes(String(view || "")) ? String(view) : String(activeView || "sales");
  if (canAccessView(wanted)) return wanted;
  if (canAccessView(activeView)) return activeView;
  return firstAccessibleViewForDomain("comptoir");
}

const VIEW_SHORTCUTS = {};

const VIEW_ICONS = {
  dashboard: "dashboard",
  caisse: "wallet",
  alerts: "alert",
  sales: "sales",
  salesHistory: "history",
  customerDebt: "wallet",
  customerJournal: "history",
  customerReturn: "rotate-ccw",
  products: "box",
  history: "trace",
  losses: "alert",
  customers: "users",
  suppliers: "truck",
  supplierJournal: "history",
  supplierReturn: "rotate-ccw",
  supplierDebt: "wallet",
  purchases: "cart",
  purchasesJournal: "history",
  finance: "wallet",
  reports: "chart",
  audit: "shield",
  backups: "backup",
  priceDisplay: "settings",
  settings: "settings"
};

const I18N_STORE = window.WILI_I18N || {};

function getI18nCatalog(lang = session.language) {
  const normalized = normalizeLangCode(lang);
  return I18N_STORE[normalized] || I18N_STORE.fr || { dict: {}, phrases: {} };
}

function getPhraseTranslation(value, lang = session.language) {
  const srcValue = String(value == null ? "" : value);
  if (!srcValue) return "";
  const catalog = getI18nCatalog(lang);
  const phrasesMap = catalog && catalog.phrases ? catalog.phrases : {};
  if (Object.prototype.hasOwnProperty.call(phrasesMap, srcValue)) return normalizeUiText(phrasesMap[srcValue]);
  return "";
}

function t(key) {
  const srcKey = String(key || "");
  const dict = getI18nCatalog(session.language).dict || {};
  if (Object.prototype.hasOwnProperty.call(dict, srcKey)) return normalizeUiText(dict[srcKey]);
  return normalizeUiText(srcKey.replace(/^(btn|opt|ph|nav|sub|kpi|dash)_/, "").replaceAll("_", " "));
}

function normalizeLangCode(value) {
  const raw = String(value || "fr").trim().toLowerCase();
  if (raw === "ar-dz") return "dz";
  if (raw === "ar" || raw.startsWith("ar-")) return "ar";
  if (raw === "dz" || raw.startsWith("dz-")) return "dz";
  return "fr";
}

function langText(frValue, arValue, dzValue = arValue) {
  const lang = normalizeLangCode(session.language);
  const translated = getPhraseTranslation(frValue, lang);
  if (translated) return translated;
  if (lang === "dz") return normalizeUiText(dzValue);
  return normalizeUiText(lang === "ar" ? arValue : frValue);
}

function normalizeUiText(value) {
  const src = String(value == null ? "" : value);
  if (!src) return src;
  const cp1252 = {
    0x20ac: 0x80, 0x201a: 0x82, 0x0192: 0x83, 0x201e: 0x84,
    0x2026: 0x85, 0x2020: 0x86, 0x2021: 0x87, 0x02c6: 0x88,
    0x2030: 0x89, 0x0160: 0x8a, 0x2039: 0x8b, 0x0152: 0x8c,
    0x017d: 0x8e, 0x2018: 0x91, 0x2019: 0x92, 0x201c: 0x93,
    0x201d: 0x94, 0x2022: 0x95, 0x2013: 0x96, 0x2014: 0x97,
    0x02dc: 0x98, 0x2122: 0x99, 0x0161: 0x9a, 0x203a: 0x9b,
    0x0153: 0x9c, 0x017e: 0x9e, 0x0178: 0x9f
  };
  const cp1252Codes = new Set(Object.keys(cp1252).map((k) => Number(k)));
  const utf8Decoder = new TextDecoder("utf-8");
  const isByteLike = (cp) => cp <= 0xff || cp1252Codes.has(cp);
  const hasMojibake = (input) => {
    for (const ch of String(input || "")) {
      const cp = ch.codePointAt(0);
      if (cp === 0x00c3 || cp === 0x00d8 || cp === 0x00d9 || cp === 0x00c2) return true;
      if (cp >= 0x80 && cp <= 0x9f) return true;
      if (cp1252Codes.has(cp)) return true;
    }
    return false;
  };
  const cleanup = (input) => String(input || "").replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F-\u009F]/g, "");
  const score = (input) => {
    let s = 0;
    for (const ch of String(input || "")) {
      const cp = ch.codePointAt(0);
      if ((cp >= 0x0600 && cp <= 0x06ff) || (cp >= 0x0750 && cp <= 0x077f) || (cp >= 0x08a0 && cp <= 0x08ff)) {
        s += 4;
      } else if ((cp >= 0x20 && cp <= 0x7e) || ch === "\n" || ch === "\r" || ch === "\t") {
        s += 1;
      } else if (cp === 0x00d8 || cp === 0x00d9 || cp === 0x00c3 || cp === 0x00c2) {
        s -= 3;
      } else if (cp >= 0x80 && cp <= 0x9f) {
        s -= 6;
      } else if (cp === 0xfffd) {
        s -= 8;
      }
    }
    return s;
  };
  const decodeBytes = (input) => {
    const bytes = [];
    for (const ch of String(input || "")) {
      const cp = ch.codePointAt(0);
      if (cp <= 0xff) bytes.push(cp);
      else if (Object.prototype.hasOwnProperty.call(cp1252, cp)) bytes.push(cp1252[cp]);
      else return input;
    }
    return utf8Decoder.decode(new Uint8Array(bytes));
  };
  const pickBest = (...candidates) => {
    let best = candidates[0];
    let bestScore = score(cleanup(best));
    for (const cand of candidates.slice(1)) {
      const cleaned = cleanup(cand);
      const sc = score(cleaned);
      if (sc > bestScore) {
        best = cleaned;
        bestScore = sc;
      }
    }
    return cleanup(best);
  };

  if (!hasMojibake(src)) return cleanup(src);
  let out = src;
  for (let pass = 0; pass < 4; pass += 1) {
    let changed = false;
    let next = "";
    let chunk = "";
    const flush = () => {
      if (!chunk) return;
      const one = decodeBytes(chunk);
      const two = decodeBytes(one);
      const best = pickBest(chunk, one, two);
      if (best !== chunk) changed = true;
      next += best;
      chunk = "";
    };
    for (const ch of out) {
      const cp = ch.codePointAt(0);
      if (isByteLike(cp)) chunk += ch;
      else {
        flush();
        next += ch;
      }
    }
    flush();
    next = cleanup(next);
    if (!changed || next === out) {
      out = next;
      break;
    }
    out = next;
  }
  return cleanup(out);
}

function normalizeDomMojibake(root = document.body) {
  if (!root) return;
  const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
  const textNodes = [];
  while (walker.nextNode()) textNodes.push(walker.currentNode);
  for (const node of textNodes) {
    const fixed = normalizeUiText(node.nodeValue || "");
    if (fixed !== node.nodeValue) node.nodeValue = fixed;
  }
  const attrs = ["placeholder", "title", "aria-label", "value"];
  root.querySelectorAll("*").forEach((el) => {
    for (const attr of attrs) {
      const raw = el.getAttribute(attr);
      if (!raw) continue;
      const fixed = normalizeUiText(raw);
      if (fixed !== raw) el.setAttribute(attr, fixed);
    }
  });
}

function trText(value) {
  const srcValue = String(value || "");
  const lang = normalizeLangCode(session.language);
  if (!srcValue) return "";
  const dict = getI18nCatalog(lang).dict || {};
  if (Object.prototype.hasOwnProperty.call(dict, srcValue)) return normalizeUiText(dict[srcValue]);
  const phrase = getPhraseTranslation(srcValue, lang);
  if (phrase) return phrase;
  if (lang === "fr") return normalizeUiText(srcValue);
  if (srcValue.startsWith("Client: ")) return normalizeUiText((lang === "dz" ? "\u0643\u0644\u064a\u0648\u0646: " : "\u0632\u0628\u0648\u0646: ") + srcValue.slice(8));
  if (srcValue.startsWith("Fournisseur: ")) return normalizeUiText((lang === "dz" ? "\u0641\u0631\u0646\u0633\u0648\u0631: " : "\u0645\u0648\u0631\u062f: ") + srcValue.slice(13));
  if (srcValue.startsWith("Produit ")) return normalizeUiText((lang === "dz" ? "\u0645\u0646\u062a\u0648\u062c " : "\u0645\u0646\u062a\u062c ") + srcValue.slice(8));
  const replacements = lang === "dz"
    ? [["Qte","\u0627\u0644\u0643\u0645\u064a\u0629"],["Stock","\u0627\u0644\u0633\u062a\u0648\u0643"],["Exp","\u0627\u0644\u0635\u0644\u0627\u062d\u064a\u0629"],["Barcode","\u0628\u0627\u0631 \u0643\u0648\u062f"],["Achat","\u0634\u0631\u0627\u0621"],["Gros","\u062c\u0645\u0644\u0629"],["Detail","\u062a\u0641\u0635\u064a\u0644"],["Aide","\u062a\u062e\u0641\u064a\u0636"],["Total","\u0627\u0644\u0625\u062c\u0645\u0627\u0644\u064a"],["Paye","\u0627\u0644\u0645\u062f\u0641\u0648\u0639"],["Reste","\u0627\u0644\u0628\u0627\u0642\u064a"],["Credit","\u0643\u0631\u064a\u062f\u064a"],["Date","\u0627\u0644\u062a\u0627\u0631\u064a\u062e"],["Montant","\u0627\u0644\u0645\u0628\u0644\u063a"],["Note","\u0645\u0644\u0627\u062d\u0638\u0629"]]
    : [["Qte","\u0627\u0644\u0643\u0645\u064a\u0629"],["Stock","\u0627\u0644\u0645\u062e\u0632\u0648\u0646"],["Exp","\u0627\u0644\u0635\u0644\u0627\u062d\u064a\u0629"],["Barcode","\u0628\u0627\u0631 \u0643\u0648\u062f"],["Achat","\u0634\u0631\u0627\u0621"],["Gros","\u062c\u0645\u0644\u0629"],["Detail","\u062a\u0641\u0635\u064a\u0644"],["Aide","\u062a\u062e\u0641\u064a\u0636"],["Total","\u0627\u0644\u0625\u062c\u0645\u0627\u0644\u064a"],["Paye","\u0627\u0644\u0645\u062f\u0641\u0648\u0639"],["Reste","\u0627\u0644\u0628\u0627\u0642\u064a"],["Credit","\u062f\u064a\u0646"],["Date","\u0627\u0644\u062a\u0627\u0631\u064a\u062e"],["Montant","\u0627\u0644\u0645\u0628\u0644\u063a"],["Note","\u0645\u0644\u0627\u062d\u0638\u0629"]];
  let out = srcValue;
  for (const [frToken, translated] of replacements) out = out.replaceAll(frToken, translated);
  return normalizeUiText(out);
}


function esc(v) {
  return String(v || "").replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;").replaceAll("'", "&#39;");
}

function iconSvg(name) {
  const icons = {
    dashboard: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-10h8V3h-8v8z"/></svg>`,
    alert: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.8-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>`,
    sales: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M7 4v6m10-6v6M5 10h14l-1 9H6l-1-9z"/></svg>`,
    history: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7M3 4v5h5m4-2v6l4 2"/></svg>`,
    wallet: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18v12H3zM16 12h5M6 7V5h12v2"/></svg>`,
    box: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 7 9-4 9 4-9 4-9-4zm0 0v10l9 4 9-4V7m-9 4v10"/></svg>`,
    trace: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h4l2-5 4 10 2-5h6"/></svg>`,
    users: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2m17 0v-2a4 4 0 0 0-3-3.9M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm9 0a4 4 0 1 0 0-8"/></svg>`,
    truck: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 5h13v11H1zM14 8h4l3 3v5h-7V8zm-9 9a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm14 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>`,
    cart: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h2l2.2 11.3a2 2 0 0 0 2 1.7H18a2 2 0 0 0 2-1.6L22 7H7m3 13a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/></svg>`,
    chart: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5m0 14h16M8 17V9m4 8V6m4 11v-4"/></svg>`,
    shield: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 4 6v6c0 5 3.5 8.5 8 10 4.5-1.5 8-5 8-10V6l-8-3z"/></svg>`,
    backup: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7M3 4v5h5m4-2v8m0 0-3-3m3 3 3-3"/></svg>`,
    settings: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8.5A3.5 3.5 0 1 0 12 15.5 3.5 3.5 0 0 0 12 8.5zm8 3.5-.8-.3a7.8 7.8 0 0 0-.4-1l.5-.7-1.9-1.9-.7.5c-.3-.2-.7-.3-1-.4L14 6h-4l-.3.8c-.4.1-.7.2-1 .4l-.7-.5L6.1 8.6l.5.7c-.2.3-.3.7-.4 1L5.4 12l.8.3c.1.4.2.7.4 1l-.5.7 1.9 1.9.7-.5c.3.2.7.3 1 .4l.3.8h4l.3-.8c.4-.1.7-.2 1-.4l.7.5 1.9-1.9-.5-.7c.2-.3.3-.7.4-1L20 12z"/></svg>`,
    edit: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16v4zm9-13 4 4"/></svg>`,
    delete: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14"/></svg>`,
    refresh: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.6-6.4M21 4v6h-6"/></svg>`,
    search: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.3-4.3m1.3-5.2a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/></svg>`,
    add: `<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>`
  };
  return icons[name] || icons.settings;
}

function iconForView(view) {
  return VIEW_ICONS[view] || "settings";
}

function setIconText(el, iconName, text = "") {
  if (!el) return;
  const labelEl = el.querySelector("[data-i18n]") || el.querySelector("span:not(.hot)");
  const labelKey = String(labelEl?.getAttribute("data-i18n") || el.getAttribute("data-i18n") || "").trim();
  const hotEl = el.querySelector("[data-shortcut-hot]");
  const hotAction = String(hotEl?.getAttribute("data-shortcut-hot") || "").trim();
  const hotText = String(hotEl?.textContent || "").trim();
  const label = String(text || "").trim() || (labelKey ? t(labelKey) : String(labelEl?.textContent || el.textContent || "").trim());
  const labelAttr = labelKey ? ` data-i18n="${esc(labelKey)}"` : "";
  const hotHtml = hotAction ? `<span class="hot shortcut-inline-hot" data-shortcut-hot="${esc(hotAction)}">${esc(hotText)}</span>` : "";
  el.innerHTML = `${iconSvg(iconName)}<span${labelAttr}>${esc(label)}</span>${hotHtml}`;
  el.classList.add("with-icon");
}

function decorateUiWithIcons() {
  document.querySelectorAll(".domain-btn").forEach((btn) => {
    const domain = String(btn.getAttribute("data-domain") || "");
    const icon = domain === "comptoir" ? "sales"
      : domain === "stock" ? "box"
      : domain === "customers" ? "users"
      : domain === "suppliers" ? "truck"
      : "settings";
    const label = String(btn.querySelector("span:not(.hot)")?.textContent || "").trim();
    const hot = String(btn.dataset.hot || btn.querySelector(".hot")?.textContent || "").trim();
    if (hot) btn.dataset.hot = hot;
    btn.innerHTML = `${iconSvg(icon)}<span>${esc(label)}</span>${hot ? `<span class="hot">${esc(hot)}</span>` : ""}`;
    btn.classList.add("with-icon");
  });

  const byIdIcons = [
    ["alertsRefreshBtn", "refresh"],
    ["posValidateBtn", "sales"],
    ["posClearBtn", "delete"],
    ["posClearBtn2", "delete"],
    ["posPrintCartBtn", "backup"],
    ["salesHistPdfBtn", "backup"],
    ["salesHistPrintSelectedBtn", "backup"],
    ["purchasesHistPdfBtn", "backup"],
    ["purchasesHistPrintSelectedBtn", "backup"],
    ["purchaseAddLineBtn", "add"],
    ["purchaseInlineToggleBtn", "add"],
    ["purchaseInlineCreateBtn", "add"],
    ["purchaseClearBtn", "delete"],
    ["purchasePdfFcBtn", "backup"],
    ["purchaseDebtPayBtn", "wallet"],
    ["saleDebtPayBtn", "wallet"],
    ["backupCreateBtn", "backup"],
    ["backupImportBtn", "backup"],
    ["priceDisplayOpenBtn", "add"],
    ["priceDisplayRefreshBtn", "refresh"],
    ["priceDisplayCloseAllBtn", "delete"],
    ["auditLoadBtn", "search"],
    ["auditPdfBtn", "backup"],
    ["reportPdfBtn", "backup"],
    ["reportDebtCustomersPdfBtn", "backup"],
    ["reportDebtSuppliersPdfBtn", "backup"],
    ["productSubmitBtn", "add"],
    ["productCancelBtn", "delete"],
    ["customerSubmitBtn", "add"],
    ["supplierSubmitBtn", "add"]
  ];
  byIdIcons.forEach(([id, icon]) => setIconText(byId(id), icon));

  if (ui.notifBtn) {
    const labelEl = ui.notifBtn.querySelector("span:not(.notif-badge)");
    const label = String(labelEl?.textContent || "").trim() || "Alertes";
    if (!ui.notifBtn.querySelector("svg.ui-icon")) {
      ui.notifBtn.insertAdjacentHTML("afterbegin", iconSvg("alert"));
    }
    const liveLabel = ui.notifBtn.querySelector("span:not(.notif-badge)");
    if (liveLabel) liveLabel.textContent = label;
    ui.notifBtn.classList.add("with-icon");
  }

  const h2Icons = [
    ["#view-dashboard .view-head h2", "dashboard"],
    ["#view-alerts .view-head h2", "alert"],
    ["#view-sales .view-head h2", "sales"],
    ["#view-salesHistory .view-head h2", "history"],
    ["#view-customerDebt .view-head h2", "wallet"],
    ["#view-customerJournal .view-head h2", "history"],
    ["#view-supplierJournal .view-head h2", "history"],
    ["#view-supplierDebt .view-head h2", "wallet"],
    ["#view-products .view-head h2", "box"],
    ["#view-history .view-head h2", "trace"],
    ["#view-customers .view-head h2", "users"],
    ["#view-suppliers .view-head h2", "truck"],
    ["#view-purchases .view-head h2", "cart"],
    ["#view-purchasesJournal .view-head h2", "history"],
    ["#view-finance .view-head h2", "wallet"],
    ["#view-reports .view-head h2", "chart"],
    ["#view-audit .view-head h2", "shield"],
    ["#view-backups .view-head h2", "backup"],
    ["#view-priceDisplay .view-head h2", "settings"],
    ["#view-settings .view-head h2", "settings"]
  ];
  h2Icons.forEach(([selector, icon]) => {
    const el = document.querySelector(selector);
    if (el) setIconText(el, icon);
  });
}

function money(cents) {
  return (Number(cents || 0) / 100).toFixed(2);
}

function fmtDateTime(value) {
  const raw = String(value || "").trim();
  if (!raw) return "-";
  const date = new Date(raw);
  if (!Number.isNaN(date.getTime())) {
    const pad = (n) => String(n).padStart(2, "0");
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
  }
  if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return `${raw} 00:00:00`;
  return raw.replace("T", " ").replace("Z", "").slice(0, 19);
}

function refYearFromDate(value) {
  const y = String(value || "").slice(0, 4);
  if (/^\d{4}$/.test(y)) return y;
  return String(new Date().getFullYear());
}

function normalizeUiRefPrefix(value, fallback = "DOC") {
  const raw = String(value || "").trim().toUpperCase();
  if (!raw) return String(fallback || "DOC").trim().toUpperCase() || "DOC";
  const safe = raw.replace(/[^A-Z0-9]/g, "");
  return safe || (String(fallback || "DOC").trim().toUpperCase() || "DOC");
}

function inferUiRowRefPrefix(row) {
  const fromDoc = normalizeUiRefPrefix(row && row.doc_type, "");
  if (fromDoc === "BL" || fromDoc === "VCC" || fromDoc === "RC" || fromDoc === "BR" || fromDoc === "FC" || fromDoc === "RF") {
    return fromDoc;
  }
  if (row && Object.prototype.hasOwnProperty.call(row, "customer_id")) {
    return String(row.customer_id || "").trim() ? "BL" : "VCC";
  }
  if (row && Object.prototype.hasOwnProperty.call(row, "supplier_id")) {
    return "BR";
  }
  return "DOC";
}

function parseUiPublicRef(value, fallbackPrefix = "DOC") {
  const raw = String(value || "").trim().toUpperCase();
  if (!raw) return null;
  let m = raw.match(/^([A-Z0-9]+)\/(\d+)\/(\d{4})$/);
  if (m) {
    return {
      prefix: normalizeUiRefPrefix(m[1], fallbackPrefix),
      seq: Math.max(0, Number(m[2]) || 0),
      year: String(m[3] || "")
    };
  }
  m = raw.match(/^(\d+)\/(\d{4})$/);
  if (m) {
    return {
      prefix: normalizeUiRefPrefix(fallbackPrefix, "DOC"),
      seq: Math.max(0, Number(m[1]) || 0),
      year: String(m[2] || "")
    };
  }
  return null;
}

function buildPublicRefMap(rows) {
  const src = Array.isArray(rows) ? rows.slice() : [];
  src.sort((a, b) => {
    const byDate = String(a.created_at || "").localeCompare(String(b.created_at || ""));
    if (byDate !== 0) return byDate;
    return String(a.id || "").localeCompare(String(b.id || ""));
  });
  const byPrefixYearSeq = new Map();
  const out = new Map();
  src.forEach((r) => {
    const id = String(r && r.id ? r.id : "");
    if (!id) return;
    const fallbackPrefix = inferUiRowRefPrefix(r || {});
    const fallbackYear = refYearFromDate(r && r.created_at);
    const explicitRef = String(r && r.public_ref ? r.public_ref : "").trim();
    const parsed = parseUiPublicRef(explicitRef, fallbackPrefix);
    if (parsed) {
      const key = `${parsed.prefix}/${parsed.year}`;
      const current = Number(byPrefixYearSeq.get(key) || 0);
      if (parsed.seq > current) byPrefixYearSeq.set(key, parsed.seq);
      out.set(id, `${parsed.prefix}/${parsed.seq}/${parsed.year}`);
      return;
    }
    const key = `${fallbackPrefix}/${fallbackYear}`;
    const next = Number(byPrefixYearSeq.get(key) || 0) + 1;
    byPrefixYearSeq.set(key, next);
    out.set(id, `${fallbackPrefix}/${next}/${fallbackYear}`);
  });
  return out;
}

function lookupPublicRef(refMap, id, createdAt = "", rowsHint = null) {
  const sid = String(id || "");
  if (!sid) return "-";
  if (refMap && refMap.has(sid)) return refMap.get(sid);
  if (Array.isArray(rowsHint) && rowsHint.length) {
    const hintMap = buildPublicRefMap(rowsHint);
    if (hintMap.has(sid)) return hintMap.get(sid);
  }
  return `?/${refYearFromDate(createdAt)}`;
}

function salePublicRef(id, createdAt = "", rowsHint = null) {
  return lookupPublicRef(salesPublicRefMap, id, createdAt, rowsHint || salesRows);
}

function purchasePublicRef(id, createdAt = "", rowsHint = null) {
  return lookupPublicRef(purchasesPublicRefMap, id, createdAt, rowsHint || purchaseRows);
}

function formatTransactionRef(refId, refType = "", createdAt = "") {
  const id = String(refId || "");
  const t = String(refType || "").toLowerCase();
  if (!id) return "-";
  if (t.includes("sale")) return salePublicRef(id, createdAt);
  if (t.includes("purchase")) return purchasePublicRef(id, createdAt);
  return `?/${refYearFromDate(createdAt)}`;
}

function toDay(v) {
  const raw = String(v || "").trim();
  if (!raw) return "";
  if (/^\d{4}-\d{2}-\d{2}/.test(raw)) return raw.slice(0, 10);
  const d = new Date(raw);
  if (Number.isNaN(d.getTime())) return "";
  const pad = (n) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

function toCents(value) {
  return Math.round((Number(value || 0) || 0) * 100);
}

function intQty(value) {
  const n = Math.floor(Number(value || 0));
  return Number.isFinite(n) ? Math.max(0, n) : 0;
}

function qtyNum(value, fallback = 0) {
  const raw = Number(value);
  const base = Number.isFinite(raw) ? raw : Number(fallback || 0);
  const rounded = Math.round(base * 100) / 100;
  return Number.isFinite(rounded) ? Math.max(0, rounded) : 0;
}

function qtyText(value, fallback = 0) {
  const out = qtyNum(value, fallback).toFixed(2);
  return out.replace(/\.?0+$/, "");
}

function withTimeout(promise, ms, timeoutCode = "timeout") {
  return new Promise((resolve, reject) => {
    const timer = setTimeout(() => {
      const err = new Error(timeoutCode);
      err.code = timeoutCode;
      reject(err);
    }, Math.max(1000, Number(ms || 0)));
    Promise.resolve(promise).then(
      (value) => {
        clearTimeout(timer);
        resolve(value);
      },
      (error) => {
        clearTimeout(timer);
        reject(error);
      }
    );
  });
}

function trimDebugText(value, max = 220) {
  const v = String(value == null ? "" : value);
  return v.length > max ? `${v.slice(0, max)}...` : v;
}

function getDebugTargetMeta(target) {
  const el = target && target.nodeType === 1 ? target : null;
  if (!el) return {};
  const id = trimDebugText(el.id || "", 80);
  const name = trimDebugText(el.getAttribute("name") || "", 80);
  const cls = trimDebugText(el.className || "", 140);
  const tag = String(el.tagName || "").toLowerCase();
  return { tag, id, name, class: cls };
}

function pushUiDebugEvent(type, details = {}) {
  const entry = {
    ts: new Date().toISOString(),
    type: String(type || "ui"),
    view: String(activeView || ""),
    domain: String(activeDomain || ""),
    details: details && typeof details === "object" ? details : { value: trimDebugText(details, 160) }
  };
  debugUiRing.push(entry);
  if (debugUiRing.length > DEBUG_UI_RING_MAX) {
    debugUiRing.splice(0, debugUiRing.length - DEBUG_UI_RING_MAX);
  }
}

function shouldSendDebug(kind, minGapMs = 1000) {
  const now = Date.now();
  const key = String(kind || "general");
  const last = Number(debugReportThrottle.get(key) || 0);
  if ((now - last) < Math.max(0, Number(minGapMs || 0))) return false;
  debugReportThrottle.set(key, now);
  return true;
}

function sendDebugReport(kind, error = null, extra = {}) {
  if (!window.wiliDebug || typeof window.wiliDebug.report !== "function") return;
  const errObj = error ? {
    name: trimDebugText(error.name || "", 80),
    message: trimDebugText(error.message || String(error || ""), 500),
    stack: trimDebugText(error.stack || "", 2500)
  } : null;
  const payload = {
    kind: String(kind || "event"),
    at: new Date().toISOString(),
    user: session.user ? {
      id: String(session.user.id || ""),
      username: String(session.user.username || ""),
      role: String(session.user.role || "")
    } : null,
    state: {
      active_view: String(activeView || ""),
      active_domain: String(activeDomain || ""),
      sale_submit_in_flight: Boolean(saleSubmitInFlight),
      sales_post_commit_refresh_in_flight: Boolean(salesPostCommitRefreshInFlight),
      pos_validate_disabled: Boolean(ui.posValidateBtn && ui.posValidateBtn.disabled),
      search_rows: Number((searchRows || []).length),
      cart_lines: Number((posCart || []).length)
    },
    error: errObj,
    extra: extra && typeof extra === "object" ? extra : { note: trimDebugText(extra, 300) },
    recent_ui_events: debugUiRing.slice(-80)
  };
  window.wiliDebug.report(payload).catch(() => {});
}

function forceEnableSalesControls() {
  const controls = document.querySelectorAll("#view-sales input, #view-sales select, #view-sales button, #view-sales textarea");
  controls.forEach((el) => {
    el.disabled = false;
    if ("readOnly" in el) el.readOnly = false;
    if (el && el.style && el.style.pointerEvents === "none") el.style.pointerEvents = "auto";
  });
}

function pushSalesRuntimeNotice(message) {
  if (!ui.salesList) return;
  ui.salesList.insertAdjacentHTML("afterbegin", row("Systeme", message));
}

function recoverSalesInteractivity(reason = "") {
  pushUiDebugEvent("sales.recover.request", { reason: trimDebugText(reason, 120) });
  saleSubmitInFlight = false;
  stopSaleSubmitWatchdog();
  salesPostCommitRefreshInFlight = false;
  forceEnableSalesControls();
  if (ui.posValidateBtn) ui.posValidateBtn.disabled = false;
  if (document.body?.style?.pointerEvents === "none") document.body.style.pointerEvents = "auto";
  if (appRoot?.style?.pointerEvents === "none") appRoot.style.pointerEvents = "auto";
  const salesView = byId("view-sales");
  if (salesView?.style?.pointerEvents === "none") salesView.style.pointerEvents = "auto";
  if (activeView === "sales") {
    renderCart();
    updatePosTotals();
    renderPosSearch(ui.posSearchInput?.value || "");
    setTimeout(() => ui.posSearchInput?.focus(), 0);
  }
  if (reason) {
    pushSalesRuntimeNotice(langText(
      `Recuperation automatique activee (${reason})`,
      `تمت استعادة الإدخال تلقائيا (${reason})`,
      `رجّعنا الإدخال تلقائيا (${reason})`
    ));
    if (shouldSendDebug(`recover:${reason}`, 800)) {
      sendDebugReport("sales_recover", null, {
        reason: trimDebugText(reason, 120),
        frozen_probe: isSalesLikelyFrozen()
      });
    }
  }
}

function isSalesLikelyFrozen() {
  const view = byId("view-sales");
  if (!view) return false;
  if (view.style?.pointerEvents === "none") return true;
  if (document.body?.style?.pointerEvents === "none") return true;
  const hasDisabledInput = Array.from(view.querySelectorAll("input, select, textarea"))
    .some((el) => Boolean(el.disabled));
  if (hasDisabledInput) return true;
  if (!saleSubmitInFlight && ui.posValidateBtn && ui.posValidateBtn.disabled) return true;
  return false;
}

function queueSalesPostCommitRefresh() {
  if (salesPostCommitRefreshInFlight) return;
  salesPostCommitRefreshInFlight = true;
  setTimeout(async () => {
    try {
      const safe = async (label, fn) => {
        try {
          if (typeof fn === "function") await fn();
        } catch (err) {
          console.error(`post_sale_refresh_failed:${label}`, err);
        }
      };
      await Promise.all([
        safe("products", () => refreshProducts({ full: false, reloadCache: true })),
        safe("customers", refreshCustomers),
        safe("suppliers", refreshSuppliers)
      ]);
      await Promise.all([
        safe("sales", refreshSales),
        safe("dashboard", refreshDashboard),
        safe("finance", refreshFinance),
        safe("caisse", refreshCaisseState),
        safe("debt", refreshDebtSummary)
      ]);
      // Keep dependent pages in sync immediately (clients credit/journals/debt selectors).
      if (typeof updateCustomerGrid === "function") updateCustomerGrid();
      if (activeView === "customerJournal") await safe("customer_trace", refreshCustomerTrace);
      if (activeView === "customerDebt") await safe("debt_invoice_options", () => refreshDebtInvoiceOptions(ui.saleDebtCustomer?.value || ""));
      renderAlerts();
      if (activeView === "sales") renderPosSearch(ui.posSearchInput?.value || "");
    } finally {
      salesPostCommitRefreshInFlight = false;
      if (activeView === "sales") forceEnableSalesControls();
    }
  }, 0);
}

function startSaleSubmitWatchdog() {
  if (saleSubmitWatchdog) clearTimeout(saleSubmitWatchdog);
  saleSubmitWatchdog = setTimeout(() => {
    if (!saleSubmitInFlight) return;
    saleSubmitInFlight = false;
    forceEnableSalesControls();
    if (ui.posValidateBtn) ui.posValidateBtn.disabled = false;
    ui.salesList?.insertAdjacentHTML("afterbegin", row(
      "Erreur",
      langText("Le traitement de la vente a pris trop de temps", "معالجة البيع استغرقت وقتا طويلا", "عملية البيع طولات بزاف")
    ));
  }, SALE_SUBMIT_TIMEOUT_MS + 1500);
}

function stopSaleSubmitWatchdog() {
  if (!saleSubmitWatchdog) return;
  clearTimeout(saleSubmitWatchdog);
  saleSubmitWatchdog = null;
}

function normalizeUiFontScale(value) {
  const key = String(value || "medium").trim().toLowerCase();
  return Object.prototype.hasOwnProperty.call(UI_FONT_SCALE_TO_SIZE, key) ? key : "medium";
}

function normalizeUiDisplayProfile(value) {
  const key = String(value || "1080").trim().toLowerCase();
  return (key === "720" || key === "768" || key === "900" || key === "1080") ? key : "1080";
}

const uiTypographyState = {
  pendingTimer: null,
  lastSavedKey: ""
};

function currentUiTypographyPrefs() {
  return {
    ui_font_size: normalizeUiFontScale(ui.setUiFontSize?.value || "medium"),
    ui_font_bold: Boolean(ui.setUiFontBold?.checked)
  };
}

function uiTypographyKey(prefs) {
  const scale = normalizeUiFontScale(prefs && prefs.ui_font_size != null ? prefs.ui_font_size : "medium");
  const bold = Boolean(prefs && prefs.ui_font_bold);
  return `${scale}|${bold ? 1 : 0}`;
}

async function persistUiTypographyPrefs(options = {}) {
  const opts = options || {};
  const prefs = currentUiTypographyPrefs();
  const key = uiTypographyKey(prefs);
  if (!opts.force && key === uiTypographyState.lastSavedKey) {
    return { ok: true, data: prefs };
  }
  const out = await window.wiliDb.updateSettings({
    ui_font_size: prefs.ui_font_size,
    ui_font_bold: prefs.ui_font_bold
  });
  if (out && out.ok) {
    uiTypographyState.lastSavedKey = key;
  }
  return out;
}

function scheduleUiTypographyPersist() {
  if (uiTypographyState.pendingTimer) clearTimeout(uiTypographyState.pendingTimer);
  uiTypographyState.pendingTimer = setTimeout(async () => {
    uiTypographyState.pendingTimer = null;
    const out = await persistUiTypographyPrefs();
    if (!out || !out.ok) showSettingsSaveMsg(true);
  }, 120);
}

function applyUiTypography(prefs = null) {
  const scale = normalizeUiFontScale(
    prefs && prefs.ui_font_size != null
      ? prefs.ui_font_size
      : (ui.setUiFontSize?.value || "medium")
  );
  const bold = prefs && prefs.ui_font_bold != null
    ? Boolean(prefs.ui_font_bold)
    : Boolean(ui.setUiFontBold?.checked);
  const root = document.documentElement;
  root.style.setProperty("--ui-font-size", UI_FONT_SCALE_TO_SIZE[scale] || UI_FONT_SCALE_TO_SIZE.medium);
  root.style.setProperty("--ui-zoom", UI_FONT_SCALE_TO_ZOOM[scale] || UI_FONT_SCALE_TO_ZOOM.medium);
  root.style.setProperty("--ui-font-weight", bold ? "700" : "400");
}

function setMsg(el, text, isError = false) {
  el.textContent = trText(text || "");
  el.classList.toggle("error", Boolean(isError));
}

let settingsSaveMsgTimer = null;
function showSettingsSaveMsg(isError = false) {
  if (!ui.settingsSaveMsg) return;
  const msg = isError
    ? langText("Echec enregistrement parametres", "فشل حفظ الإعدادات", "فشل حفظ ليبارامتر")
    : langText("Parametres enregistres avec succes", "تم حفظ الإعدادات بنجاح", "ليبارامتر تسجلو بنجاح");
  if (typeof appNotify === "function") appNotify(msg, Boolean(isError), isError ? 4200 : 2600);
  ui.settingsSaveMsg.textContent = msg;
  ui.settingsSaveMsg.classList.toggle("error", Boolean(isError));
  ui.settingsSaveMsg.classList.remove("hidden");
  if (settingsSaveMsgTimer) clearTimeout(settingsSaveMsgTimer);
  settingsSaveMsgTimer = setTimeout(() => {
    ui.settingsSaveMsg.classList.add("hidden");
    ui.settingsSaveMsg.textContent = "";
  }, 2200);
}

let salesRuntimeMsgTimer = null;
function showSalesRuntimeMsg(text, isError = false, timeoutMs = 4500) {
  if (!ui.salesRuntimeMsg) return;
  ui.salesRuntimeMsg.textContent = trText(String(text || ""));
  ui.salesRuntimeMsg.classList.toggle("error", Boolean(isError));
  ui.salesRuntimeMsg.classList.remove("hidden");
  if (salesRuntimeMsgTimer) clearTimeout(salesRuntimeMsgTimer);
  if (timeoutMs > 0) {
    salesRuntimeMsgTimer = setTimeout(() => {
      if (!ui.salesRuntimeMsg) return;
      ui.salesRuntimeMsg.classList.add("hidden");
      ui.salesRuntimeMsg.classList.remove("error");
      ui.salesRuntimeMsg.textContent = "";
    }, timeoutMs);
  }
}

let confirmResolver = null;
function closeAppConfirm(result) {
  const fn = confirmResolver;
  confirmResolver = null;
  ui.confirmModal?.classList.add("hidden");
  if (typeof fn === "function") fn(Boolean(result));
}

let returnResolver = null;
function closeReturnModal(payload = null) {
  const fn = returnResolver;
  returnResolver = null;
  ui.returnModal?.classList.add("hidden");
  if (typeof fn === "function") fn(payload);
}

function appAskReturnPayload(options = {}) {
  return new Promise((resolve) => {
    if (!ui.returnModal || !ui.returnQtyInput || !ui.returnNoteInput || !ui.returnOkBtn || !ui.returnCancelBtn) {
      resolve(null);
      return;
    }
    if (returnResolver) closeReturnModal(null);
    returnResolver = resolve;
    const title = String(options && options.title ? options.title : langText("Retour", "ارجاع", "رتور"));
    if (ui.returnTitle) ui.returnTitle.textContent = trText(title);
    ui.returnQtyInput.value = qtyText(Math.max(0.01, qtyNum(options.defaultQty || 1, 1)));
    ui.returnNoteInput.value = String(options.defaultNote || "");
    ui.returnModal.classList.remove("hidden");
    setTimeout(() => ui.returnQtyInput?.focus(), 0);
  });
}

let userPinResolver = null;
function closeUserPinModal(pinValue = null) {
  const fn = userPinResolver;
  userPinResolver = null;
  ui.userPinModal?.classList.add("hidden");
  if (ui.userPinValue) ui.userPinValue.value = "";
  if (ui.userPinConfirm) ui.userPinConfirm.value = "";
  if (typeof fn === "function") fn(pinValue);
}

function appAskUserPin(options = {}) {
  return new Promise((resolve) => {
    if (!ui.userPinModal || !ui.userPinValue || !ui.userPinConfirm || !ui.userPinOkBtn || !ui.userPinCancelBtn) {
      resolve(null);
      return;
    }
    if (userPinResolver) closeUserPinModal(null);
    userPinResolver = resolve;
    if (ui.userPinTitle) {
      ui.userPinTitle.textContent = trText(String(options.title || langText("Modifier mot de passe", "تغيير كلمة المرور", "بدل الموطو باس")));
    }
    ui.userPinOkBtn.textContent = trText(String(options.okText || langText("Enregistrer", "حفظ", "سجّل")));
    ui.userPinCancelBtn.textContent = trText(String(options.cancelText || langText("Annuler", "إلغاء", "إلغاء")));
    ui.userPinValue.value = "";
    ui.userPinConfirm.value = "";
    ui.userPinModal.classList.remove("hidden");
    setTimeout(() => ui.userPinValue?.focus(), 0);
  });
}

let barcodeSizeResolver = null;
function closeBarcodeSizePicker(sizeKey = "") {
  const fn = barcodeSizeResolver;
  barcodeSizeResolver = null;
  ui.barcodeSizeModal?.classList.add("hidden");
  if (typeof fn === "function") fn(String(sizeKey || "").trim());
}

function appPickBarcodeSize() {
  return new Promise((resolve) => {
    if (!ui.barcodeSizeModal || !ui.barcodeSizeSmallBtn || !ui.barcodeSizeMediumBtn || !ui.barcodeSizeLargeBtn || !ui.barcodeSizeCancelBtn) {
      resolve("medium");
      return;
    }
    if (barcodeSizeResolver) closeBarcodeSizePicker("");
    barcodeSizeResolver = resolve;
    if (ui.barcodeSizeTitle) ui.barcodeSizeTitle.textContent = langText("Taille etiquette barcode", "حجم ملصق الباركود", "حجم ليتيكيت باركود");
    if (ui.barcodeSizeMessage) ui.barcodeSizeMessage.textContent = langText("Choisir la taille d'impression", "اختر حجم الطباعة", "اختار حجم الطباعة");
    ui.barcodeSizeSmallBtn.textContent = langText("Small 20x40", "صغير 20x40", "صغير 20x40");
    ui.barcodeSizeMediumBtn.textContent = langText("Medium 30x50", "متوسط 30x50", "متوسط 30x50");
    ui.barcodeSizeLargeBtn.textContent = langText("Large 40x60", "كبير 40x60", "كبير 40x60");
    ui.barcodeSizeCancelBtn.textContent = langText("Annuler", "إلغاء", "إلغاء");
    ui.barcodeSizeModal.classList.remove("hidden");
    setTimeout(() => ui.barcodeSizeMediumBtn?.focus(), 0);
  });
}

let domMojibakeObserverStarted = false;
function ensureDomMojibakeObserver() {
  if (domMojibakeObserverStarted || !document?.body || typeof MutationObserver === "undefined") return;
  const obs = new MutationObserver((mutations) => {
    for (const m of mutations) {
      if (m.type === "characterData" && m.target) {
        const fixed = normalizeUiText(m.target.nodeValue || "");
        if (fixed !== m.target.nodeValue) m.target.nodeValue = fixed;
      }
      if (m.type === "childList") {
        m.addedNodes.forEach((n) => {
          if (!n) return;
          if (n.nodeType === Node.TEXT_NODE) {
            const fixed = normalizeUiText(n.nodeValue || "");
            if (fixed !== n.nodeValue) n.nodeValue = fixed;
            return;
          }
          if (n.nodeType === Node.ELEMENT_NODE) normalizeDomMojibake(n);
        });
      }
    }
  });
  obs.observe(document.body, { subtree: true, childList: true, characterData: true });
  domMojibakeObserverStarted = true;
}

const BUTTON_INTENT_CLASSES = [
  "btn-action-danger",
  "btn-action-pdf",
  "btn-action-print",
  "btn-action-edit",
  "btn-action-filter",
  "btn-action-add",
  "btn-action-save",
  "btn-action-pay",
  "btn-action-backup",
  "btn-action-clear",
  "btn-action-neutral"
];
let buttonIntentObserverStarted = false;

function textHasAny(haystack, needles) {
  return needles.some((needle) => haystack.includes(needle));
}

function classifyButtonIntent(btn) {
  if (!btn || !btn.classList) return;
  const attrs = Array.from(btn.attributes || []).map((a) => `${a.name}=${a.value}`).join(" ");
  const hay = `${btn.id || ""} ${btn.className || ""} ${attrs} ${btn.textContent || ""}`.toLowerCase();
  const isActionButton = btn.matches(".btn, .btn-mini, .btn-square, .btn-compact, [data-action], [data-p-del], [data-s-del], [data-c-del], [data-u-del], [data-purchase-delete], [data-sale-delete], [data-pdf]");
  if (!isActionButton) return;
  btn.classList.remove(...BUTTON_INTENT_CLASSES);

  const isDanger = textHasAny(hay, [
    "delete", "remove", "supprimer", "حذف", "مسح", "fermer", "close", "revoke"
  ]) || btn.matches("[data-p-del], [data-s-del], [data-c-del], [data-u-del], [data-purchase-delete], [data-sale-delete]");
  const isClear = textHasAny(hay, ["vider", "effacer", "clear", "reset", "annuler", "إلغاء", "تفريغ"]);
  const isPdf = textHasAny(hay, [
    "pdf", "facture", "bon de reception", "bon de livraison", "devis", "recu", "reçu",
    "barcode", "code barre", "historique", "export"
  ]);
  const isPrint = textHasAny(hay, ["imprimer", "print", "طباعة", "طبع"]);
  const isEdit = textHasAny(hay, ["modifier", "edit", "تعديل"]) || btn.matches("[data-p-edit], [data-s-edit], [data-c-edit]");
  const isBackup = textHasAny(hay, ["sauvegarde", "backup", "restore", "importer", "archive"]);
  const isPay = textHasAny(hay, ["paiement", "reglement", "règlement", "payer", "credit", "crédit"]);
  const isFilter = textHasAny(hay, ["filtrer", "filter", "rechercher", "chercher", "search", "بحث"]);
  const isAdd = textHasAny(hay, ["ajouter", "add", "nouveau", "creer", "créer", "create"]);
  const isSave = textHasAny(hay, ["enregistrer", "valider", "confirmer", "activer", "save", "confirm"]);

  if (isDanger) btn.classList.add("btn-action-danger");
  else if (isClear) btn.classList.add("btn-action-clear");
  else if (isPdf) btn.classList.add("btn-action-pdf");
  else if (isPrint) btn.classList.add("btn-action-print");
  else if (isEdit) btn.classList.add("btn-action-edit");
  else if (isBackup) btn.classList.add("btn-action-backup");
  else if (isPay) btn.classList.add("btn-action-pay");
  else if (isFilter) btn.classList.add("btn-action-filter");
  else if (isAdd) btn.classList.add("btn-action-add");
  else if (isSave) btn.classList.add("btn-action-save");
  else if (btn.classList.contains("btn-soft") || btn.classList.contains("btn-mini") || btn.classList.contains("btn-square") || btn.classList.contains("btn-compact")) {
    btn.classList.add("btn-action-neutral");
  }
}

function applyButtonIntentColors(root = document) {
  const scope = root && typeof root.querySelectorAll === "function" ? root : document;
  scope.querySelectorAll(".btn, .btn-mini, .btn-square, .btn-compact, button[data-action], button[data-p-del], button[data-s-del], button[data-c-del], button[data-u-del], button[data-purchase-delete], button[data-sale-delete], button[data-pdf]").forEach(classifyButtonIntent);
}

function startButtonIntentObserver() {
  if (buttonIntentObserverStarted || !document?.body || typeof MutationObserver === "undefined") {
    applyButtonIntentColors(document);
    return;
  }
  const obs = new MutationObserver((mutations) => {
    let dirty = false;
    for (const m of mutations) {
      if (m.type === "childList" && m.addedNodes.length) {
        dirty = true;
        break;
      }
      if (m.type === "characterData") {
        dirty = true;
        break;
      }
    }
    if (dirty) requestAnimationFrame(() => applyButtonIntentColors(document));
  });
  obs.observe(document.body, { subtree: true, childList: true, characterData: true });
  buttonIntentObserverStarted = true;
  applyButtonIntentColors(document);
}

function appConfirm(message, opts = {}) {
  return new Promise((resolve) => {
    const title = String(opts && opts.title ? opts.title : langText("Confirmation", "تأكيد", "تأكيد"));
    const okText = String(opts && opts.okText ? opts.okText : langText("Confirmer", "تأكيد", "تأكيد"));
    const cancelText = String(opts && opts.cancelText ? opts.cancelText : langText("Annuler", "إلغاء", "إلغاء"));
    if (!ui.confirmModal || !ui.confirmMessage || !ui.confirmOkBtn || !ui.confirmCancelBtn) {
      appNotify(message || "confirmation_required", true, 2400);
      resolve(false);
      return;
    }
    if (confirmResolver) closeAppConfirm(false);
    confirmResolver = resolve;
    if (ui.confirmTitle) ui.confirmTitle.textContent = trText(title);
    ui.confirmMessage.textContent = trText(String(message || ""));
    ui.confirmOkBtn.textContent = trText(okText);
    ui.confirmCancelBtn.textContent = trText(cancelText);
    ui.confirmModal.classList.remove("hidden");
    setTimeout(() => ui.confirmOkBtn?.focus(), 0);
  });
}

function appNotify(message, isError = false, timeoutMs = 3200) {
  const host = ui.appToastStack;
  if (!host) {
    showSalesRuntimeMsg(message, isError, timeoutMs);
    return;
  }
  const node = document.createElement("div");
  node.className = `app-toast${isError ? " error" : ""}`;
  node.textContent = trText(String(message || ""));
  host.prepend(node);
  const ttl = Math.max(900, Number(timeoutMs || 0));
  setTimeout(() => node.remove(), ttl);
}

let purchaseDebtMsgTimer = null;
function showPurchaseDebtMsg(text, isError = false) {
  if (!ui.purchaseDebtMsg) return;
  ui.purchaseDebtMsg.textContent = trText(text || "");
  ui.purchaseDebtMsg.classList.toggle("error", Boolean(isError));
  ui.purchaseDebtMsg.classList.remove("hidden");
  if (purchaseDebtMsgTimer) clearTimeout(purchaseDebtMsgTimer);
  purchaseDebtMsgTimer = setTimeout(() => {
    ui.purchaseDebtMsg.classList.add("hidden");
    ui.purchaseDebtMsg.textContent = "";
  }, 3200);
}

let saleDebtMsgTimer = null;
function showSaleDebtMsg(text, isError = false) {
  if (!ui.saleDebtMsg) return;
  ui.saleDebtMsg.textContent = trText(text || "");
  ui.saleDebtMsg.classList.toggle("error", Boolean(isError));
  ui.saleDebtMsg.classList.remove("hidden");
  if (saleDebtMsgTimer) clearTimeout(saleDebtMsgTimer);
  saleDebtMsgTimer = setTimeout(() => {
    ui.saleDebtMsg.classList.add("hidden");
    ui.saleDebtMsg.textContent = "";
  }, 3200);
}

let userSaveMsgTimer = null;
function showUserSaveMsg(isError = false, raw = "") {
  if (!ui.userSaveMsg) return;
  const fallback = isError
    ? langText("Echec ajout utilisateur", "فشل إضافة المستخدم", "فشل زيادة ليوزر")
    : langText("Utilisateur ajoute avec succes", "تمت إضافة المستخدم بنجاح", "ليوزر تزاد بنجاح");
  const errorCode = String(raw || "").trim();
  const msg = errorCode ? trText(errorCode) : fallback;
  ui.userSaveMsg.textContent = msg || fallback;
  ui.userSaveMsg.classList.toggle("error", Boolean(isError));
  ui.userSaveMsg.classList.remove("hidden");
  if (userSaveMsgTimer) clearTimeout(userSaveMsgTimer);
  userSaveMsgTimer = setTimeout(() => {
    ui.userSaveMsg.classList.add("hidden");
    ui.userSaveMsg.textContent = "";
  }, 2600);
}

function isNegativeStockBlockedUi() {
  if (cache.settings && Object.prototype.hasOwnProperty.call(cache.settings, "allow_negative_stock")) {
    return !Boolean(cache.settings.allow_negative_stock);
  }
  return String(ui.setAllowNegativeStock?.value || "false") !== "true";
}

function stockForProduct(productId) {
  const p = cache.products.find((x) => String(x.id || "") === String(productId || ""));
  return p ? Number(p.stock_qty || 0) : 0;
}

function notifyInsufficientStock(productName, availableQty, requestedQty) {
  const msg = langText(
    `Stock insuffisant pour ${productName}. Disponible: ${availableQty}, demande: ${requestedQty}.`,
    `المخزون غير كاف للمنتج ${productName}. المتوفر: ${availableQty}، المطلوب: ${requestedQty}.`,
    `الستوك ما يكفيش للمنتوج ${productName}. المتوفر: ${availableQty}، المطلوب: ${requestedQty}.`
  );
  pushUiDebugEvent("sales.stock_insufficient", {
    product: trimDebugText(String(productName || "-"), 120),
    available: Number(availableQty || 0),
    requested: Number(requestedQty || 0)
  });
  if (shouldSendDebug("sales_stock_insufficient", 1200)) {
    sendDebugReport("sales_stock_insufficient", null, {
      product: trimDebugText(String(productName || "-"), 120),
      available: Number(availableQty || 0),
      requested: Number(requestedQty || 0)
    });
  }
  // Avoid blocking alert() in POS; keep UI interactive and show the normal toast feedback.
  saleSubmitInFlight = false;
  stopSaleSubmitWatchdog();
  forceEnableSalesControls();
  if (ui.posValidateBtn) ui.posValidateBtn.disabled = false;
  appNotify(msg, true, 5200);
  setTimeout(() => ui.posSearchInput?.focus(), 0);
}

function row(title, sub) {
  return `<div class="row"><div><strong>${esc(trText(title))}</strong><div class="small">${esc(trText(sub || ""))}</div></div></div>`;
}

function detailSummaryGrid(items = []) {
  const cells = (Array.isArray(items) ? items : []).map((item) => {
    const label = item && item.label != null ? item.label : "";
    const value = item && item.value != null ? item.value : "";
    const accent = item && item.accent ? " is-accent" : "";
    return `<div class="detail-summary-item${accent}"><span>${esc(trText(label))}</span><strong>${esc(trText(value))}</strong></div>`;
  }).join("");
  return `<div class="detail-summary-grid">${cells}</div>`;
}

function updateSortHeaderIndicators(tableEl, sortKey, sortDir) {
  if (!tableEl) return;
  const key = String(sortKey || "");
  const dir = String(sortDir || "asc").toLowerCase() === "desc" ? "desc" : "asc";
  tableEl.querySelectorAll("thead th[data-sort]").forEach((th) => {
    const thKey = String(th.getAttribute("data-sort") || "");
    const active = thKey === key;
    th.classList.toggle("sort-active", active);
    th.classList.toggle("sort-asc", active && dir === "asc");
    th.classList.toggle("sort-desc", active && dir === "desc");
    if (!active) {
      th.classList.remove("sort-asc");
      th.classList.remove("sort-desc");
    }
  });
}

function setText(selector, value) {
  const el = document.querySelector(selector);
  if (el) el.textContent = normalizeUiText(value);
}

function setTextAll(selector, values) {
  const nodes = Array.from(document.querySelectorAll(selector));
  if (!nodes.length) return;
  if (Array.isArray(values)) {
    nodes.forEach((el, idx) => {
      if (values[idx] != null) el.textContent = normalizeUiText(values[idx]);
    });
    return;
  }
  nodes.forEach((el) => {
    el.textContent = normalizeUiText(String(values || ""));
  });
}

function setRadioChipTexts(selector, labels) {
  const nodes = Array.from(document.querySelectorAll(selector));
  if (!nodes.length || !Array.isArray(labels)) return;
  nodes.forEach((el, idx) => {
    const input = el.querySelector("input");
    if (!input || labels[idx] == null) return;
    Array.from(el.childNodes).forEach((n) => {
      if (n !== input) el.removeChild(n);
    });
    el.appendChild(document.createTextNode(` ${normalizeUiText(labels[idx])}`));
  });
}

function setPlaceholder(id, value) {
  const el = byId(id);
  if (el) el.setAttribute("placeholder", normalizeUiText(value));
}

function fallbackLabelById(id) {
  const map = {
    setAllowNegativeStock: langText("Stock negatif", "المخزون السالب", "الستوك السالب"),
    setLanguage: langText("Langue", "اللغة", "اللغة"),
    setCurrency: langText("Devise", "العملة", "الدفيز"),
    setUiFontSize: langText("Taille police", "حجم الخط", "حجم الخط"),
    setUiDisplayProfile: langText("Profil affichage", "مقاس العرض", "قياس لافيشاج"),
    saleCustomer: langText("Client", "الزبون", "لكليان"),
    purchaseSupplier: langText("Fournisseur", "المورد", "لفرنسور"),
    purchaseProduct: langText("Produit", "المنتج", "المنتوج"),
    financeType: langText("Type mouvement", "نوع الحركة", "نوع الحركة"),
    reportDate: langText("Date rapport", "تاريخ التقرير", "تاريخ التقرير"),
    historyProduct: langText("Produit", "المنتج", "المنتوج"),
    historyStatsFrom: langText("Du", "من", "من"),
    historyStatsTo: langText("Au", "إلى", "إلى"),
    historyStatsCompare: langText("Comparaison", "المقارنة", "المقارنة"),
    historyType: langText("Type", "النوع", "النوع"),
    historyFrom: langText("Du", "من", "من"),
    historyTo: langText("Au", "إلى", "إلى"),
    historyRef: langText("Reference", "المرجع", "المرجع"),
    historyUser: langText("Utilisateur", "المستخدم", "ليوزر"),
    customerTraceCustomer: langText("Client", "الزبون", "لكليان"),
    customerTraceFrom: langText("Du", "من", "من"),
    customerTraceTo: langText("Au", "إلى", "إلى"),
    customerTraceType: langText("Type", "النوع", "النوع"),
    supplierTraceSupplier: langText("Fournisseur", "المورد", "لفرنسور"),
    supplierTraceFrom: langText("Du", "من", "من"),
    supplierTraceTo: langText("Au", "إلى", "إلى"),
    supplierTraceType: langText("Type", "النوع", "النوع"),
    saleDebtCustomer: langText("Client (credit)", "الزبون (كريدي)", "لكليان (كريدي)"),
    saleDebtInvoice: langText("Facture due", "فاتورة الكريدي", "فاتورة الكريدي"),
    purchaseDebtSupplier: langText("Fournisseur (credit)", "المورد (كريدي)", "لفرنسور (كريدي)"),
    purchaseDebtInvoice: langText("Facture due", "فاتورة الكريدي", "فاتورة الكريدي"),
    productGridSort: langText("Tri", "الترتيب", "الترتيب"),
    userRole: langText("Role", "الدور", "الرول"),
    auditFrom: langText("Du", "من", "من"),
    auditTo: langText("Au", "إلى", "إلى")
  };
  return map[id] || "";
}

function labelTextFromControl(ctrl) {
  const explicit = String(ctrl.getAttribute("data-label") || "").trim();
  const i18nLabel = String(ctrl.getAttribute("data-i18n-label") || "").trim();
  const phKey = String(ctrl.getAttribute("data-i18n-ph") || "").trim();
  const placeholder = String(ctrl.getAttribute("placeholder") || "").trim();
  if (i18nLabel) return t(i18nLabel);
  if (explicit) return trText(explicit);
  if (phKey) return t(phKey);
  if (placeholder) return trText(placeholder);
  return fallbackLabelById(String(ctrl.id || ""));
}

function ensureFormLabels() {
  const controls = Array.from(document.querySelectorAll("#appRoot input, #appRoot select, #appRoot textarea, #gateRoot input, #gateRoot select, #gateRoot textarea"));
  controls.forEach((ctrl) => {
    const tag = String(ctrl.tagName || "").toLowerCase();
    const type = String(ctrl.getAttribute("type") || "").toLowerCase();
    if (type === "hidden" || type === "checkbox" || type === "radio" || type === "button" || type === "submit" || ctrl.classList.contains("hidden")) return;
    if (ctrl.dataset.noAutoLabel === "1") return;
    if (ctrl.closest(".row-check")) return;
    if (!ctrl.id) return;

    const text = normalizeUiText(String(labelTextFromControl(ctrl) || "").trim());
    if (!text) return;

    let wrapper = ctrl.parentElement && ctrl.parentElement.classList.contains("form-field")
      ? ctrl.parentElement
      : null;
    if (!wrapper) {
      wrapper = document.createElement("div");
      wrapper.className = "form-field";
      ctrl.parentNode.insertBefore(wrapper, ctrl);
      wrapper.appendChild(ctrl);
    }

    let label = wrapper.querySelector("label.auto-field-label");
    if (!label) {
      label = document.createElement("label");
      label.className = "auto-field-label";
      wrapper.insertBefore(label, ctrl);
    }
    label.setAttribute("for", ctrl.id);
    label.textContent = text;

    if (tag === "select" && !ctrl.getAttribute("title")) ctrl.setAttribute("title", text);
  });
}

function applyStaticTranslations() {
  const catalog = getI18nCatalog(session.language);
  if (catalog && typeof catalog.applyStaticTranslations === "function") {
    catalog.applyStaticTranslations({
      session,
      ui,
      t,
      trText,
      langText,
      normalizeUiText,
      setText,
      setTextAll,
      setPlaceholder,
      setRadioChipTexts
    });
  }

setText("#financePrevBtn", langText("Precedent", "السابق", "لسابق"));
  setText("#financeNextBtn", langText("Suivant", "التالي", "لتالي"));
  setText("#financeApplyBtn", langText("Filtrer", "فلترة", "فلتر"));
  setText("#financeAnalysisApplyBtn", langText("Analyser", "تحليل", "حلل"));
  setText('#financeTable thead th[data-sort="created_at"]', langText("Date", "التاريخ", "لتاريخ"));
  setText('#financeTable thead th[data-sort="source_label"]', langText("Type", "النوع", "النوع"));
  setText('#financeTable thead th[data-sort="public_ref"]', langText("Ref", "المرجع", "الريف"));
  setText('#financeTable thead th[data-sort="party_name"]', langText("Tiers", "الطرف", "الطرف"));
  setText('#financeTable thead th[data-sort="products_text"]', langText("Produits", "المنتجات", "المنتوجات"));
  setText('#financeTable thead th[data-sort="qty_total"]', langText("Qte", "الكمية", "لكمية"));
  setText('#financeTable thead th[data-sort="amount_cents"]', langText("Montant", "المبلغ", "لمونطان"));
  setText('#financeTable thead th[data-sort="status_label"]', langText("Statut", "الحالة", "الحالة"));
  setText("#auditPrevBtn", langText("Precedent", "السابق", "لسابق"));
  setText("#auditNextBtn", langText("Suivant", "التالي", "لتالي"));
  setText("#view-customers .view-head p", langText("Gestion fiches clients et operations", "إدارة ملفات الزبائن والعمليات", "تسيير ملفات لكليان والعمليات"));
  setText("#view-suppliers .view-head p", langText("Gestion fournisseurs et suivi achats", "إدارة الموردين ومتابعة المشتريات", "تسيير لفرنسور وتتبع لمشتريات"));
  setText("#view-finance .view-head p", langText("Encaissement, decaissement et suivi caisse", "إدخال وإخراج الأموال ومتابعة الصندوق", "دخل وخرج الدراهم وتتبع لكاسة"));
  setText("#view-reports .view-head p", langText("Suivi des credit clients/fournisseurs et export PDF", "متابعة كريدي الزبائن والموردين وتصدير PDF", "متابعة كريدي لكليان ولفرنسور مع PDF"));
  setText("#view-backups .view-head p", langText("Sauvegarde locale et restauration", "نسخ احتياطي محلي واسترجاع", "سوفغارد محلي وترجيع"));
  setText("#view-priceDisplay .view-head p", langText("Ouvrir un ou plusieurs ecrans de scan prix (sans mouvement stock)", "فتح شاشة او عدة شاشات لعرض السعر فقط", "تفتح شاشة ولا بزاف شاشات للثمن فقط"));
  setText("#view-settings .view-head p", langText("Configuration systeme et utilisateurs", "إعدادات النظام والمستخدمين", "اعدادات السيستام واليوزر"));
  setText("#supplierNameLbl", langText("Nom", "الاسم", "الاسم"));
  setText("#supplierPhoneLbl", langText("Telephone", "الهاتف", "تليفون"));
  setText("#supplierNifLbl", "NIF");
  setText("#supplierNisLbl", "NIS");
  setText("#supplierRcLbl", "RC");
  setPlaceholder("productGridSearch", langText("Recherche produit / code barre / reference", "بحث المنتج / بار كود / المرجع", "بحث المنتوج / بار كود / الريفيرونس"));
  setPlaceholder("customerGridSearch", langText("Recherche client / telephone", "بحث الزبون / الهاتف", "بحث لكليان / تليفون"));
  setPlaceholder("supplierGridSearch", langText("Recherche fournisseur / telephone", "بحث المورد / الهاتف", "بحث لفرنسور / تليفون"));
  setPlaceholder("supplierNif", "NIF");
  setPlaceholder("supplierNis", "NIS");
  setPlaceholder("supplierRc", "RC");
  setPlaceholder("financeAnalysisFrom", langText("Du", "من", "من"));
  setPlaceholder("financeAnalysisTo", langText("Au", "إلى", "لـ"));
  setPlaceholder("financeFrom", langText("Du", "من", "من"));
  setPlaceholder("financeTo", langText("Au", "إلى", "لـ"));
  setPlaceholder("financeSearch", langText("Recherche: ref / tiers / produit", "بحث: المرجع / الطرف / المنتج", "فتّش: الريف / الطرف / المنتوج"));
  setText('#financePageSize option[value="10"]', langText("10 / page", "10 / صفحة", "10 / صفحة"));
  setText('#financePageSize option[value="20"]', langText("20 / page", "20 / صفحة", "20 / صفحة"));
  setText('#financePageSize option[value="50"]', langText("50 / page", "50 / صفحة", "50 / صفحة"));
  setText("#supplierOpenFcBtn", langText("Facture FC", "فاتورة FC", "فاكتور FC"));
  setText('#customerGridSort option[value="name_asc"]', langText("Nom (A-Z)", "الاسم (أ-ي)", "الاسم (A-Z)"));
  setText('#customerGridSort option[value="name_desc"]', langText("Nom (Z-A)", "الاسم (ي-أ)", "الاسم (Z-A)"));
  setText('#customerGridSort option[value="phone_asc"]', langText("Telephone (A-Z)", "الهاتف (تصاعدي)", "تليفون (A-Z)"));
  setText('#customerGridSort option[value="date_desc"]', langText("Date (recent)", "التاريخ (الأحدث)", "التاريخ (لجديد)"));
  setTextAll("#customersTable thead th", [
    langText("Nom", "الاسم", "الاسم"),
    langText("Telephone", "الهاتف", "تليفون"),
    langText("Solde/Avoir", "رصيد/أفوار", "رصيد/أفوار"),
    langText("Actions", "إجراءات", "إجراءات")
  ]);
  setTextAll("#suppliersTable thead th", [
    langText("Nom", "الاسم", "الاسم"),
    langText("Telephone", "الهاتف", "تليفون"),
    langText("Solde/Avoir", "رصيد/أفوار", "رصيد/أفوار"),
    langText("Actions", "إجراءات", "إجراءات")
  ]);
  setText("#saleDiscountLabel", langText("Remise total", "خصم على الإجمالي", "روميز على التوتال"));
  setText("#saleDiscountTypeLabel", langText("Montant", "نوع الخصم", "نوع الروميز"));
  setText('#saleDiscountType option[value="amount"]', langText("Montant", "قيمة ثابتة", "نسبة"));
  setText('#saleDiscountType option[value="percent"]', langText("Pourcentage %", "نسبة %", "بورصونتاج %"));
  setText("#saleDiscountReset", langText("Effacer", "مسح", "امسح"));
  setPlaceholder("saleDiscountValue", langText("Remise", "الخصم", "الروميز"));
  setPlaceholder("saleCustomerSearch", langText("Client / telephone / reference", "الزبون / الهاتف / المرجع", "لكليان / تليفون / ريفيرانس"));
  setPlaceholder("purchaseSupplierSearch", langText("Fournisseur / telephone / reference", "المورد / الهاتف / المرجع", "لفرنسور / تليفون / ريفيرانس"));
  setPlaceholder("purchaseProductSearch", trText("Produit / barcode / reference"));
  setText("#purchaseTotalLabel", langText("Total achat", "إجمالي الشراء", "توتال الشرا"));
  setText("#purchaseDueLabel", langText("Reste", "المتبقي", "لباقي"));
  setText('#purchaseDocType option[value="BR"]', langText("BR - Bon de reception", "BR - وصل استلام", "BR - بون ريسبسيون"));
  setText('#purchaseDocType option[value="FC"]', langText("FC - Facture fournisseur (R)", "FC - فاتورة مورد (R)", "FC - فاكتور فورنيسور (R)"));
  setText("#purchaseDocHint", langText("BR: achat avec stock | FC: facture fournisseur (R)", "BR: شراء مع المخزون | FC: فاتورة مورد (R)", "BR: شرا مع الستوك | FC: فاكتور فورنيسور (R)"));
  setPlaceholder("historyFrom", langText("Du", "من", "من"));
  setPlaceholder("historyTo", langText("Au", "إلى", "لـ"));
  setPlaceholder("historyStatsFrom", langText("Du", "من", "من"));
  setPlaceholder("historyStatsTo", langText("Au", "إلى", "لـ"));
  setPlaceholder("historyRef", langText("Facture / Ref", "فاتورة / مرجع", "فاكتورة / ريفيرونس"));
  setPlaceholder("historyUser", langText("Utilisateur", "المستخدم", "ليوزر"));
  setPlaceholder("purchaseNewName", trText("Nom produit"));
  setPlaceholder("purchaseNewBarcode", trText("Barcode"));
  setPlaceholder("purchaseNewCost", trText("Prix achat"));
  setPlaceholder("purchaseNewWholesale", trText("Prix gros"));
  setPlaceholder("purchaseNewRetail", trText("Prix detail"));
  setPlaceholder("purchaseNewAssist", trText("Prix remise"));
  setText("#purchaseInlineToggleBtn", langText("Nouveau produit rapide", "منتج سريع جديد"));
  setText("#purchaseInlineCreateBtn", langText("Creer", "انشاء + اضافة"));

  // Coverage for recently added pages/filters/tables to keep AR/FR/DZ fully aligned.
  setTextAll("#productsTable thead th", [
    langText("Nom", "الاسم", "الاسم"),
    langText("Code barre", "بار كود", "بار كود"),
    langText("Reference", "\u0627\u0644\u0645\u0631\u062C\u0639", "\u0627\u0644\u0631\u064A\u0641\u0631\u0648\u0646\u0633"),
    langText("Stock", "المخزون", "الستوك"),
    langText("Achat", "الشراء", "الشرا"),
    langText("Gros", "الجملة", "جملة"),
    langText("Detail", "التفصيل", "تفصيل"),
    langText("Remise", "تخفيض", "تخفيض"),
    langText("Expiration", "الصلاحية", "الاكسبيراسيون"),
    langText("Actions", "إجراءات", "إجراءات")
  ]);
  setTextAll("#view-history .data-grid thead th", [
    langText("Date", "التاريخ", "التاريخ"),
    langText("Type", "النوع", "النوع"),
    langText("Produit", "المنتج", "المنتوج"),
    langText("Qte", "الكمية", "الكمية"),
    langText("Prix unit", "سعر الوحدة", "سعر الوحدة"),
    langText("Total", "الإجمالي", "المجموع"),
    "Ref",
    langText("Utilisateur", "المستخدم", "ليوزر"),
    langText("Note", "ملاحظة", "ملاحظة")
  ]);
  setText("#historyFilterBtn", langText("Filtrer", "تصفية", "فيلتري"));
  setText('.history-tab-btn[data-history-tab="stats"]', langText("Statistiques", "إحصائيات", "ستاتيستيك"));
  setText('.history-tab-btn[data-history-tab="archive"]', langText("Archive", "الأرشيف", "لارشيف"));
  setText("#historyForm button", langText("Charger historique", "تحميل السجل", "حمّل ليستوريك"));
  setPlaceholder("historyProductSearch", trText("Produit / barcode / reference"));
  setText(".history-stats-card .finance-card-head h3", langText("Statistiques produit", "إحصائيات المنتج", "ستاتيستيك المنتوج"));
  setText(".history-stats-card .finance-card-head p", langText("Ventes, achats, profit estime et comparaison par periode.", "المبيعات والمشتريات والربح التقديري والمقارنة حسب الفترة.", "بيع وشرا وربح تقريبي ومقارنة بالفترة."));
  setText(".history-archive-card .finance-card-head h3", langText("Archive mouvements", "أرشيف الحركات", "أرشيف الحركات"));
  setText(".history-archive-card .finance-card-head p", langText("Historique detaille des mouvements, ventes, achats et retours.", "سجل مفصل للحركات والمبيعات والمشتريات والمرتجعات.", "هيستوري مفصل للحركات والبيع والشرا والرتور."));
  setText("#historyStatsBtn", langText("Analyser", "تحليل", "حلل"));
  setText('#historyStatsCompare option[value="month"]', langText("Comparer par mois", "مقارنة حسب الشهر", "قارن بالشهر"));
  setText('#historyStatsCompare option[value="year"]', langText("Comparer par annee", "مقارنة حسب السنة", "قارن بالعام"));
  setText('#historyType option[value=""]', langText("Type: Tous", "النوع: الكل", "نوع: كول"));
  setText('#historyType option[value="sale"]', langText("Vente", "بيع", "فونت"));
  setText('#historyType option[value="purchase"]', langText("Achat", "شراء", "شرا"));
  setText('#historyType option[value="sale_return"]', langText("Retour client", "ارجاع زبون", "رتور لكليان"));
  setText('#historyType option[value="purchase_return"]', langText("Retour fournisseur", "ارجاع مورد", "رتور لفرنسور"));
  setText('#historyType option[value="stock_loss"]', langText("Perte stock", "خسارة مخزون", "خسارة ستوك"));
  setText('#historyType option[value="manual"]', langText("Ajustement", "تعديل يدوي", "تعديل مانوي"));

  setText("#view-customerJournal .sub-head", langText("Journal client", "جورنال الزبون", "جورنال لكليان"));
  setText("#view-supplierJournal .sub-head", langText("Journal fournisseur", "جورنال المورد", "جورنال لفرنسور"));
  setText("#customerTraceForm button", langText("Charger suivi client", "تحميل تتبع الزبون", "حمّل تتبّع لكليان"));
  setPlaceholder("customerTraceCustomerSearch", langText("Client / telephone / reference", "الزبون / الهاتف / المرجع", "لكليان / تليفون / ريفيرانس"));
  setPlaceholder("customerReturnCustomerSearch", langText("Client / telephone / reference", "الزبون / الهاتف / المرجع", "لكليان / تليفون / ريفيرانس"));
  setPlaceholder("customerReturnProductSearch", langText("Produit / barcode / reference", "المنتج / الباركود / المرجع", "المنتوج / باركود / ريفيرانس"));
  setPlaceholder("saleDebtCustomerSearch", langText("Client / telephone / reference", "الزبون / الهاتف / المرجع", "لكليان / تليفون / ريفيرانس"));
  setText("#supplierTraceForm button", langText("Charger suivi fournisseur", "تحميل تتبع المورد", "حمّل تتبّع لفرنسور"));
  setPlaceholder("supplierTraceSupplierSearch", langText("Fournisseur / telephone / reference", "المورد / الهاتف / المرجع", "لفرنسور / تليفون / ريفيرانس"));
  setPlaceholder("supplierReturnSupplierSearch", langText("Fournisseur / telephone / reference", "المورد / الهاتف / المرجع", "لفرنسور / تليفون / ريفيرانس"));
  setPlaceholder("supplierReturnProductSearch", langText("Produit / barcode / reference", "المنتج / الباركود / المرجع", "المنتوج / باركود / ريفيرانس"));
  setPlaceholder("purchaseDebtSupplierSearch", langText("Fournisseur / telephone / reference", "المورد / الهاتف / المرجع", "لفرنسور / تليفون / ريفيرانس"));
  setText('#customerTraceType option[value=""]', langText("Type: Tous", "النوع: الكل", "نوع: كول"));
  setText('#customerTraceType option[value="sale"]', langText("Vente", "بيع", "فونت"));
  setText('#customerTraceType option[value="payment"]', langText("Paiement", "تسديد", "ريغلمان"));
  setText('#customerTraceType option[value="sale_return"]', langText("Retour client", "ارجاع زبون", "رتور لكليان"));
  setText('#supplierTraceType option[value=""]', langText("Type: Tous", "النوع: الكل", "نوع: كول"));
  setText('#supplierTraceType option[value="purchase"]', langText("Achat", "شراء", "شرا"));
  setText('#supplierTraceType option[value="payment"]', langText("Paiement", "تسديد", "ريغلمان"));
  setText('#supplierTraceType option[value="purchase_return"]', langText("Retour fournisseur", "ارجاع مورد", "رتور لفرنسور"));

  setPlaceholder("customerTraceFrom", langText("Du", "من", "من"));
  setPlaceholder("customerTraceTo", langText("Au", "إلى", "لـ"));
  setPlaceholder("supplierTraceFrom", langText("Du", "من", "من"));
  setPlaceholder("supplierTraceTo", langText("Au", "إلى", "لـ"));
}


function toFileUrl(filePath) {
  const p = String(filePath || "").trim();
  if (!p) return "";
  const normalized = p.replace(/\\/g, "/");
  if (/^(data|blob|https?):/i.test(normalized)) return normalized;
  if (/^file:\/\//i.test(normalized)) return normalized;
  if (/^[A-Za-z]:\//.test(normalized)) return encodeURI(`file:///${normalized}`);
  return encodeURI(`file://${normalized}`);
}

function setPdfEditMode(active) {
  pdfEditMode = Boolean(active);
  ui.pdfPreviewModal?.querySelector(".pdf-modal-card")?.classList.toggle("editing", pdfEditMode);
  ui.pdfResetBtn?.classList.toggle("hidden", !pdfEditMode);
}

function prepareEditablePdfFrame() {
  const frame = ui.pdfPreviewFrame;
  const doc = frame?.contentDocument;
  if (!doc || !doc.body) return;
  doc.designMode = "on";
  doc.body.contentEditable = "true";
  doc.body.spellcheck = false;
  if (!doc.getElementById("__wili_pdf_edit_style")) {
    const style = doc.createElement("style");
    style.id = "__wili_pdf_edit_style";
    style.textContent = `
      body{outline:2px dashed rgba(15,118,110,.45);outline-offset:-8px;cursor:text}
      *{user-select:text!important}
    `;
    doc.head.appendChild(style);
  }
  doc.body.focus();
}

function loadEditablePdfHtml(html) {
  const frame = ui.pdfPreviewFrame;
  if (!frame) return;
  frame.removeAttribute("src");
  frame.srcdoc = String(html || "");
  frame.addEventListener("load", prepareEditablePdfFrame, { once: true });
  setTimeout(prepareEditablePdfFrame, 60);
}

function enterPdfEditMode() {
  if (!lastPdfEditableHtml) {
    appNotify(langText("PDF non editable", "هذا PDF لا يمكن تعديله", "هذا PDF ما يتبدلش"), true);
    return;
  }
  setPdfEditMode(true);
  loadEditablePdfHtml(lastPdfEditableHtml);
}

function resetPdfEditor() {
  if (!pdfEditMode || !lastPdfEditableHtml) return;
  loadEditablePdfHtml(lastPdfEditableHtml);
}

function getEditedPdfHtml() {
  const doc = ui.pdfPreviewFrame?.contentDocument;
  if (!doc || !doc.documentElement) return "";
  return `<!doctype html>\n${doc.documentElement.outerHTML}`;
}

function openPdfPreview(filePath, title = "PDF", suggestedName = "", editableHtml = "") {
  const srcPath = String(filePath || "").trim();
  if (!srcPath) return;
  lastPdfPath = srcPath;
  lastPdfName = String(suggestedName || srcPath.split(/[\\/]/).pop() || "document.pdf");
  lastPdfEditableHtml = String(editableHtml || "");
  setPdfEditMode(false);
  ui.pdfPreviewTitle.textContent = trText(title);
  ui.pdfPreviewFrame.removeAttribute("srcdoc");
  if (lastPdfEditableHtml) {
    ui.pdfPreviewFrame.removeAttribute("src");
    ui.pdfPreviewFrame.srcdoc = lastPdfEditableHtml;
  } else {
    ui.pdfPreviewFrame.src = toFileUrl(srcPath);
  }
  const normalizedTitle = String(title || "").toLowerCase();
  const compact = normalizedTitle.includes("panier") || normalizedTitle.includes("ticket");
  ui.pdfPreviewModal.querySelector(".pdf-modal-card")?.classList.toggle("compact", compact);
  ui.pdfPreviewModal.classList.remove("hidden");
  if (compact) setTimeout(() => ui.pdfPrintBtn?.focus(), 0);
}

function closePdfPreview() {
  ui.pdfPreviewModal.classList.add("hidden");
  setPdfEditMode(false);
  lastPdfEditableHtml = "";
  ui.pdfPreviewFrame.removeAttribute("srcdoc");
  ui.pdfPreviewFrame.src = "about:blank";
}

function handlePdfResult(out, label, targetEl = null) {
  const ok = out && out.ok && out.data && out.data.file_path;
  const filePath = ok ? String(out.data.file_path) : "";
  const suggestedName = ok ? String((out.data && out.data.suggested_name) || "") : "";
  const editableHtml = ok ? String((out.data && out.data.editable_html) || "") : "";
  const labelText = trText(label || "PDF");
  if (ok) openPdfPreview(filePath, labelText, suggestedName, editableHtml);
  const successMsg = langText("PDF pret: imprimer ou sauvegarder", "PDF جاهز: اطبع أو احفظ", "PDF واجد: اطبع ولا سافي");
  if (!targetEl) return;
  if (targetEl === ui.reportExportBox || targetEl === ui.auditStats) {
    targetEl.innerHTML = row(labelText, ok ? successMsg : (out && out.error ? out.error : "pdf_failed"));
    return;
  }
  appNotify(`${labelText}: ${ok ? successMsg : (out && out.error ? out.error : "pdf_failed")}`, !ok, ok ? 2600 : 5200);
}

function drawList(container, items, mapper) {
  if (!items || !items.length) {
    container.innerHTML = `<div class="empty">${t("no_data")}</div>`;
    return;
  }
  container.innerHTML = items.map(mapper).join("");
}

function amountChip(centsValue, kind = "net") {
  const cls = kind === "in" ? "amount-in" : (kind === "out" ? "amount-out" : "amount-net");
  return `<span class="amount-chip ${cls}">${esc(money(centsValue || 0))}</span>`;
}

function dayLeft(dateStr) {
  if (!dateStr) return null;
  const dt = new Date(String(dateStr));
  if (Number.isNaN(dt.getTime())) return null;
  return Math.ceil((dt.getTime() - Date.now()) / 86400000);
}

function dayKey(value) {
  const raw = String(value || "").trim();
  if (!raw) return "";
  const d = new Date(raw);
  if (Number.isNaN(d.getTime())) return raw.slice(0, 10);
  const pad = (n) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

function aggregateByDay(rows, dateField, valueGetter) {
  const map = new Map();
  for (const r of rows || []) {
    const k = dayKey(r && r[dateField]);
    if (!k) continue;
    map.set(k, (map.get(k) || 0) + Number(valueGetter(r) || 0));
  }
  return Array.from(map.entries()).sort((a, b) => a[0].localeCompare(b[0]));
}

function chartBars(title, items, valueFormatter = (v) => String(v)) {
  const list = (items || []).slice(0, 8);
  if (!list.length) return `<div class="chart-card"><h4>${esc(title)}</h4><div class="small">${t("no_data")}</div></div>`;
  const max = Math.max(...list.map((x) => Number(x.value || 0)), 1);
  const rows = list.map((x) => {
    const value = Number(x.value || 0);
    const w = Math.max(4, Math.round((value / max) * 100));
    return `<div class="chart-row"><span class="chart-lbl">${esc(x.label || "-")}</span><div class="chart-track"><div class="chart-fill" style="width:${w}%"></div></div><span class="chart-val">${esc(valueFormatter(value))}</span></div>`;
  }).join("");
  return `<div class="chart-card"><h4>${esc(title)}</h4>${rows}</div>`;
}

function chartDonut(title, entries) {
  const list = (entries || []).filter((x) => Number(x.value || 0) > 0);
  const total = list.reduce((s, x) => s + Number(x.value || 0), 0);
  if (!total) return `<div class="chart-card"><h4>${esc(title)}</h4><div class="small">${t("no_data")}</div></div>`;
  const colors = ["#0e7a53", "#0ea5e9", "#f59e0b", "#ef4444", "#8b5cf6", "#22c55e"];
  let acc = 0;
  const stops = list.map((x, idx) => {
    const from = acc;
    acc += (Number(x.value || 0) / total) * 100;
    return `${colors[idx % colors.length]} ${from.toFixed(2)}% ${acc.toFixed(2)}%`;
  }).join(", ");
  const legend = list.map((x, idx) => {
    const pct = ((Number(x.value || 0) / total) * 100).toFixed(1);
    return `<div class="chart-legend-row"><span class="dot" style="background:${colors[idx % colors.length]}"></span><span>${esc(x.label || "-")}</span><strong>${pct}%</strong></div>`;
  }).join("");
  return `<div class="chart-card"><h4>${esc(title)}</h4><div class="chart-donut-wrap"><div class="chart-donut" style="background: conic-gradient(${stops});"></div><div class="chart-donut-center">${esc(money(total))}</div></div><div class="chart-legend">${legend}</div></div>`;
}

function renderDashboardCharts() {
  if (!ui.dashboardCharts) return;
  const salesByDay = aggregateByDay(salesRows, "created_at", (r) => Number(r.total_cents || 0)).slice(-7).map(([k, v]) => ({ label: k.slice(5), value: v }));
  const purchasesByDay = aggregateByDay(purchaseRows, "created_at", (r) => Number(r.total_cents || 0)).slice(-7).map(([k, v]) => ({ label: k.slice(5), value: v }));
  const dueCustomers = salesRows.reduce((s, r) => s + Math.max(0, Number(r.due_cents || 0)), 0);
  const dueSuppliers = purchaseRows.reduce((s, r) => s + Math.max(0, Number(r.due_cents || 0)), 0);
  ui.dashboardCharts.innerHTML = [
    chartBars(langText("Ventes 7 jours", "مبيعات 7 أيام", "لفونت 7 أيام"), salesByDay, (v) => money(v)),
    chartBars(langText("Achats 7 jours", "مشتريات 7 أيام", "لمشتريات 7 أيام"), purchasesByDay, (v) => money(v)),
    chartDonut(langText("Structure credit", "هيكلة الكريدي", "تركيبة الكريدي"), [
      { label: langText("Clients", "الزبائن", "لكليان"), value: dueCustomers },
      { label: langText("Fournisseurs", "الموردين", "لفرنسور"), value: dueSuppliers }
    ])
  ].join("");
}

function renderSalesHistoryCharts() {
  if (!ui.salesHistoryCharts) return;
  const rows = salesRowsFiltered && salesRowsFiltered.length ? salesRowsFiltered : salesRows;
  const byDay = aggregateByDay(rows, "created_at", (r) => Number(r.total_cents || 0)).slice(-10).map(([k, v]) => ({ label: k.slice(5), value: v }));
  const byClientMap = new Map();
  for (const r of rows) {
    const name = String(r.customer_name || "-");
    byClientMap.set(name, (byClientMap.get(name) || 0) + Number(r.total_cents || 0));
  }
  const topClients = Array.from(byClientMap.entries()).sort((a, b) => b[1] - a[1]).slice(0, 8).map(([k, v]) => ({ label: k, value: v }));
  const paid = rows.reduce((s, r) => s + Number(r.paid_cents || 0), 0);
  const due = rows.reduce((s, r) => s + Number(r.due_cents || 0), 0);
  ui.salesHistoryCharts.innerHTML = [
    chartBars(langText("Ventes par jour", "المبيعات حسب اليوم", "لفونت حسب النهار"), byDay, (v) => money(v)),
    chartBars(langText("Top clients", "أفضل الزبائن", "أفضل لكليان"), topClients, (v) => money(v)),
    chartDonut(langText("Paye vs Reste", "مدفوع مقابل متبقي", "المدفوع ضد المتبقي"), [
      { label: langText("Paye", "مدفوع", "مدفوع"), value: paid },
      { label: langText("Reste", "متبقي", "باقي"), value: due }
    ])
  ].join("");
}

function renderPurchasesCharts() {
  if (!ui.purchasesCharts) return;
  const byDay = aggregateByDay(purchaseRows, "created_at", (r) => Number(r.total_cents || 0)).slice(-10).map(([k, v]) => ({ label: k.slice(5), value: v }));
  const bySupplier = new Map();
  for (const r of purchaseRows) {
    const name = String(r.supplier_name || "-");
    bySupplier.set(name, (bySupplier.get(name) || 0) + Number(r.total_cents || 0));
  }
  const topSuppliers = Array.from(bySupplier.entries()).sort((a, b) => b[1] - a[1]).slice(0, 8).map(([k, v]) => ({ label: k, value: v }));
  const paid = purchaseRows.reduce((s, r) => s + Number(r.paid_cents || 0), 0);
  const due = purchaseRows.reduce((s, r) => s + Number(r.due_cents || 0), 0);
  ui.purchasesCharts.innerHTML = [
    chartBars(langText("Achats par jour", "المشتريات حسب اليوم", "لمشتريات حسب النهار"), byDay, (v) => money(v)),
    chartBars(langText("Top fournisseurs", "أفضل الموردين", "أفضل لفرنسور"), topSuppliers, (v) => money(v)),
    chartDonut(langText("Paye vs Reste", "مدفوع مقابل متبقي", "المدفوع ضد المتبقي"), [
      { label: langText("Paye", "مدفوع", "مدفوع"), value: paid },
      { label: langText("Reste", "متبقي", "باقي"), value: due }
    ])
  ].join("");
}

function renderFinanceCharts(sourceRows) {
  if (!ui.financeCharts) return;
  const rows = Array.isArray(sourceRows) ? sourceRows : (financeRows || []);
  const byDayIn = aggregateByDay(rows.filter((x) => String(x.entry_type || "") === "income"), "created_at", (r) => Number(r.amount_cents || 0)).slice(-10).map(([k, v]) => ({ label: k.slice(5), value: v }));
  const byDayOut = aggregateByDay(rows.filter((x) => String(x.entry_type || "") === "expense"), "created_at", (r) => Number(r.amount_cents || 0)).slice(-10).map(([k, v]) => ({ label: k.slice(5), value: v }));
  const income = rows.filter((x) => String(x.entry_type || "") === "income").reduce((s, r) => s + Number(r.amount_cents || 0), 0);
  const expense = rows.filter((x) => String(x.entry_type || "") === "expense").reduce((s, r) => s + Number(r.amount_cents || 0), 0);
  ui.financeCharts.innerHTML = [
    chartBars(langText("Entrees par jour", "المداخيل حسب اليوم", "الدخول للكاسة حسب النهار"), byDayIn, (v) => money(v)),
    chartBars(langText("Sorties par jour", "المصاريف حسب اليوم", "الخروج من الكاسة حسب النهار"), byDayOut, (v) => money(v)),
    chartDonut(langText("Repartition caisse", "توزيع الصندوق", "تقسيم الكاسة"), [
      { label: langText("Entrees", "مداخيل", "دخول"), value: income },
      { label: langText("Sorties", "مصاريف", "خروج"), value: expense }
    ])
  ].join("");
}

function renderDebtCharts() {
  if (!ui.debtCharts) return;
  const d = debtSummaryState || {};
  const customers = Array.isArray(d.customers) ? d.customers : [];
  const suppliers = Array.isArray(d.suppliers) ? d.suppliers : [];
  const topCustomers = customers.slice(0, 8).map((x) => ({ label: x.name || "-", value: Number(x.due_cents || 0) }));
  const topSuppliers = suppliers.slice(0, 8).map((x) => ({ label: x.name || "-", value: Number(x.due_cents || 0) }));
  const custTotal = customers.reduce((s, x) => s + Number(x.due_cents || 0), 0);
  const suppTotal = suppliers.reduce((s, x) => s + Number(x.due_cents || 0), 0);
  ui.debtCharts.innerHTML = [
    chartBars(langText("Top credit clients", "أعلى كريدي الزبائن", "أكثر لكليان كريدي"), topCustomers, (v) => money(v)),
    chartBars(langText("Top credit fournisseurs", "أعلى كريدي الموردين", "أكثر لفرنسور كريدي"), topSuppliers, (v) => money(v)),
    chartDonut(langText("Clients vs Fournisseurs", "الزبائن مقابل الموردين", "لكليان ضد لفرنسور"), [
      { label: langText("Clients", "زبائن", "لكليان"), value: custTotal },
      { label: langText("Fournisseurs", "موردين", "لفرنسور"), value: suppTotal }
    ])
  ].join("");
}

function renderHistoryCharts() {
  if (!ui.historyCharts) return;
  const d = historyTraceState || {};
  const moves = Array.isArray(d.stock_moves) ? d.stock_moves : [];
  const salesItems = Array.isArray(d.sales_items) ? d.sales_items : [];
  const purchaseItems = Array.isArray(d.purchase_items) ? d.purchase_items : [];
  const byReason = new Map();
  for (const m of moves) {
    const reason = String(m.reason || m.ref_type || "move");
    byReason.set(reason, (byReason.get(reason) || 0) + Math.abs(Number(m.qty_delta || 0)));
  }
  const reasonRows = Array.from(byReason.entries()).sort((a, b) => b[1] - a[1]).slice(0, 8).map(([k, v]) => ({ label: k, value: v }));
  const soldQty = salesItems.reduce((s, x) => s + Math.max(0, Number(x.qty || 0)), 0);
  const boughtQty = purchaseItems.reduce((s, x) => s + Math.max(0, Number(x.qty || 0)), 0);
  const stockNow = Number((d.product && d.product.stock_qty) || 0);
  ui.historyCharts.innerHTML = [
    chartBars(langText("Mouvements par motif", "الحركات حسب السبب", "الحركات حسب السبب"), reasonRows, (v) => String(intQty(v))),
    chartDonut(langText("Vendu vs Achete", "المباع مقابل المشتَرى", "المباع ضد المشترى"), [
      { label: langText("Vendu", "مباع", "مباع"), value: soldQty },
      { label: langText("Achete", "مشتَرى", "مشترى"), value: boughtQty }
    ]),
    chartBars(langText("Etat stock", "حالة المخزون", "حالة المخزون"), [
      { label: langText("Stock actuel", "مخزون حالي", "المخزون الحالي"), value: stockNow },
      { label: langText("Vendu", "مباع", "مباع"), value: soldQty },
      { label: langText("Achete", "مشتَرى", "مشترى"), value: boughtQty }
    ], (v) => String(intQty(v)))
  ].join("");
}

function applyLanguage() {
  const lang = normalizeLangCode(session.language);
  session.language = lang;
  document.documentElement.lang = lang === "ar" ? "ar" : (lang === "dz" ? "ar-DZ" : "fr");
  document.documentElement.dir = (lang === "ar" || lang === "dz") ? "rtl" : "ltr";
  document.querySelectorAll("[data-i18n]").forEach((el) => {
    const key = String(el.getAttribute("data-i18n") || "");
    if (key) el.textContent = t(key);
  });
  document.querySelectorAll("[data-i18n-ph]").forEach((el) => {
    const key = String(el.getAttribute("data-i18n-ph") || "");
    if (key) el.setAttribute("placeholder", t(key));
  });
  renderSubnav();
  renderWorkspaceTabs(activeView);
  setHeader(activeView);
  applyStaticTranslations();
  ensureFormLabels();
  normalizeDomMojibake(document.body);
  ensureDomMojibakeObserver();
  decorateUiWithIcons();
  startButtonIntentObserver();
  // Refresh dynamic POS blocks so stale language fragments don't remain after switching language.
  renderPosSearch(ui.posSearchInput?.value || "");
  renderCart();
  updatePosTotals();
  if (typeof renderShortcutSettings === "function") renderShortcutSettings();
  if (typeof renderHistoryStats === "function") renderHistoryStats();
  if (typeof renderHistoryTableHeader === "function") renderHistoryTableHeader();
  if (typeof rerenderCaisseTranslations === "function") rerenderCaisseTranslations();
  if (typeof rerenderFinanceTranslations === "function") rerenderFinanceTranslations();
  if (typeof rerenderXlsxImportTranslations === "function") rerenderXlsxImportTranslations();
  applySalesHistoryFilters(false);
  applyPurchasesJournalFilters(false);
  updateStatusBar(activeView);
  applyButtonIntentColors(document);
}

