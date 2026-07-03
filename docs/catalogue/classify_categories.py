#!/usr/bin/env python3
"""
Auto-classify EVERY product into a shop category (ClickUp card:
"Auto-classify products into shop categories").

This real pharmacy catalogue must be categorised in full — nothing is
left blank. Keyword rules over the product name are checked in priority
order (specific categories first), and anything the rules don't catch is
inferred from its form/regulation so every product ends up in a
category. Categories beyond the original 13 were added where the
catalogue clearly needs them (Eye Care, Continence, Stop Smoking,
Diabetes & Monitoring, Baby & Child, Men's Grooming, Home & Household),
plus "Prescription Medicines" / "General Health" as final homes.

Run after clean_products.py. Reads products-clean.csv. Writes:
  products-categorised.csv        ean,name,regulation,category  (review)
  products-woo-categories.csv     SKU,Categories  (re-import to attach by SKU)
"""
import csv
import os
from collections import Counter

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "products-clean.csv")
REVIEW = os.path.join(HERE, "products-categorised.csv")
WOO = os.path.join(HERE, "products-woo-categories.csv")

# (category, [keywords]) in PRIORITY order — first match wins.
RULES = [
    ("Weight Management", [
        "wegovy", "mounjaro", "saxenda", "mysimba", "orlistat", "xenical",
        "alli ", "weight loss", "slimming", "liraglutide", "semaglutide",
        "tirzepatide", "appetite", "meal replacement",
    ]),
    ("Pet Care", [
        "frontline", "spot on", "flea", "wormer", "dewormer", "for dogs",
        "for cats", "drontal", "kitten", "puppy", " dog ", " cat ", " pet ",
        "cats and", "dogs and",
    ]),
    ("Testing & Monitoring", [
        "blood glucose", "glucose meter", "glucometer", "glucorx", "accu-chek",
        "accu chek", "lancet", "test strip", "diabet", "insulin", "ketone",
        "finger pricker", "blood pressure monitor", "bp monitor", "pulse oximeter",
        "hba1c", "covid", "test kit", "self test", "self-test", "std test",
        "cholesterol test", "health test", "pregnancy test",
    ]),
    ("Supports & Braces", [
        "scholl", "softgrip", "support ", "brace", "compression", "knee ",
        "wrist ", "ankle ", "elbow ", "tubigrip", "splint", "back support",
        "posture", "insole", "orthotic",
    ]),
    ("Stop Smoking", [
        "nicorette", "niquitin", "nicotine", "nicabate", "stop smoking",
        "smoking cessation", "nicotinell",
    ]),
    ("Continence", [
        "tena", "incontinence", "continence", "attends", "always discreet",
        "bladder weakness", "washable pants", "bed pad",
    ]),
    ("Sexual Wellness", [
        "viagra", "sildenafil", "tadalafil", "cialis", "vardenafil", "levitra",
        "spedra", "condom", "durex", "pasante", "mates ", "lubricant", "lube",
        "ky jelly", "k-y ", "erectile", "delay ", "intimate", "sylk",
    ]),
    ("Women's Health", [
        "hrt", "menopause", "evorel", "utrogestan", "oestrogen", "estradiol",
        "folic", "pregnancy test", "ovulation", "period ", "menstrual",
        "tampon", "tampax", "sanitary", "panty liner", "pantyliner",
        "maternity pad", "breast pad", "always ", "bodyform", "cystitis",
        "canesten", "thrush", "vagi", "for women", "women's", "womens",
        "female", "gyno", " uti ",
    ]),
    ("Men's Health", [
        "finasteride", "propecia", "testosterone", "prostate", "tamsulosin",
        "for men", "men's health", "erectile",
    ]),
    ("Baby & Child", [
        "baby", "infant", "toddler", "children's", "for children", "teething",
        "colic", "nappy", "nappies", "sudocrem", "calpol", "milk formula",
        "formula milk", "aptamil", " sma ", "cow & gate", "dummy", "soother",
        "weaning",
    ]),
    ("Eye Care", [
        "eye drop", "eye drops", "optrex", "contact lens", "lens solution",
        "lens plus", "dry eye", "blepha", "eye bath", "eye wash", "eye ointment",
        "clinitas", "hycosan", "hypromellose", "ocupure", "eye mask",
    ]),
    ("Oral Care", [
        "toothpaste", "toothbrush", "mouthwash", "dental", "floss", "oral-b",
        " oral b", " oral care", "corsodyl", "listerine", "denture", "sensodyne",
        "colgate", " gum ", "teeth", "whitening", "aquafresh", "interdental",
        "tongue", "mouth ulcer", "bonjela", "wisdom", "toothpick", "tepe",
    ]),
    ("Cold & Flu", [
        "cold ", "flu ", "cough", "decongest", "congestion", "sudafed",
        "lemsip", "beechams", "benylin", "sinus", "sore throat", "strepsils",
        "catarrh", "vicks", "nasal", "olbas", "covonia", "nurofen cold",
        "day & night", "day and night", "throat", "lozenge", "pastille",
        "vocalzone", "jakemans",
    ]),
    ("Pain Relief", [
        "paracetamol", "ibuprofen", "aspirin", "nurofen", "anadin", "panadol",
        "co-codamol", "cocodamol", "codeine", "naproxen", "solpadeine",
        "migraine", "voltarol", "ibuleve", "ibugel", "ibuprofen gel",
        "deep heat", "deep freeze", "pain relief", "painkiller", "headache",
        "backache", "joint ", "muscle", "diclofenac", "feminax", "aleve",
    ]),
    ("Digestive", [
        "indigestion", "heartburn", "gaviscon", "rennie", "laxative",
        "constipation", "senna", "diarrhoea", "imodium", "buscopan",
        "dulcolax", "fybogel", "probiotic", "acid reflux", "bloating",
        "peptac", "lansoprazole", "omeprazole", "ranitidine", "colpermin",
        "bowel", "stomach", "nausea", " wind ", " ibs ", "anusol", "germoloids",
        "haemorrhoid", "hemorrhoid", "preparation h", "psyllium", "husk",
    ]),
    ("First Aid", [
        "plaster", "elastoplast", "bandage", "antiseptic", "savlon",
        "germolene", "dressing", "wound", "tcp ", "sterile", "gauze",
        "first aid", "blister", "compeed", "micropore", "sting", "burn ",
        "cotton wool", "thermometer", "hibiscrub", "chlorhexidine", "plasters",
    ]),
    ("Hair Care", [
        "shampoo", "conditioner", "hair", "scalp", "dandruff", "nizoral",
        "minoxidil", "regaine", "nourkrin", "alpecin", "t/gel",
        "head & shoulders", "keratin", "styling", "hairspray", "hair spray",
        "mousse", "wella", "batiste", "toni & guy", "hair dye", "hair colour",
        "highlights", "schwarzkopf", "l'oreal elvive", "elvive", "tresemme",
    ]),
    ("Men's Grooming", [
        "shaving", "shave", "razor", "beard", "aftershave", "trimmer",
        "grooming",
    ]),
    ("Skincare", [
        "moistur", "cleanser", "cleansing", "serum", "cerave", "la roche",
        "e45", "aveeno", "eczema", "dermol", "cetraben", "diprobase", "spf",
        "sunscreen", "sun cream", "suncream", "aftersun", "after sun",
        "psoriasis", "acne", "hydrocortisone", "emollient", "lotion",
        "face wash", "face mask", "facial", "micellar", " face ", "mask",
        "hand cream", "foot cream", "body butter", "cream", "balm", "skin",
        "toner", "exfoliat", "cuticle", "verruca", "wart", "corn ", "callus",
    ]),
    ("Vitamins", [
        "vitamin", "multivit", "supplement", "omega", "cod liver", "magnesium",
        "zinc", "iron ", "calcium", "b12", "minerals", "seven seas", "centrum",
        "berocca", "probio", "collagen", "glucosamine", "biotin", "turmeric",
        "evening primrose", "co-enzyme", "coq10", "gummies", "solgar",
        "wellman", "wellwoman", "healthspan", "nutrition", "protein",
        "terranova", "pukka", "sunwarrior", "rhodiola", "herbal", "tincture",
        "electrolyte", "trace mineral", "fresubin", "ensure ", "complan",
        "aymes", "superfood", "greens", "weleda", "kiki health", "immune",
        "nutrient", "nature's aid", "natures aid", "higher nature", "bio-",
        "amino", "creatine", "pre-workout",
    ]),
    ("Beauty", [
        "make-up", "makeup", "cosmetic", "fragrance", "perfume", "cologne",
        "nail", "lipstick", "mascara", "foundation", "concealer", "gift set",
        "candle", "eyeliner", "blush", "eyeshadow", "cotton pad", "mist",
        "body wash", "shower gel", "shower", "bath", "deodorant",
        "antiperspirant", "anti-perspirant", "perspirant", "roll-on", "soap",
        "hand wash", "self tan", "fake tan", "baylis", "sanctuary", "eau de",
        "foamburst", "lynx", "sure men", "dove men",
    ]),
    ("Home & Household", [
        "disinfectant", "cleaner", "cleaning", "bleach", "diffuser",
        "air freshener", "laundry", "washing up", "detergent", "reed diffuser",
        "insect", "fly spray", "sharpener",
    ]),
]

# Non-P form inference — the last resort before the general bucket, so
# nothing is skipped. First match wins.
FALLBACK_FORMS = [
    ("tablet", "Vitamins"), ("capsule", "Vitamins"), ("softgel", "Vitamins"),
    ("effervescent", "Vitamins"), ("powder", "Vitamins"), ("sachet", "Vitamins"),
    ("drops", "Vitamins"), ("chewable", "Vitamins"),
    ("cream", "Skincare"), ("ointment", "Skincare"), ("gel", "Skincare"),
    ("oil", "Skincare"), ("lotion", "Skincare"), ("moisturis", "Skincare"),
    ("spray", "Beauty"), ("wash", "Beauty"), ("wipe", "Beauty"),
    ("scrub", "Beauty"), ("butter", "Beauty"),
]


def classify(name, regulation):
    n = " " + name.lower() + " "
    for category, keywords in RULES:
        for kw in keywords:
            if kw in n:
                return category
    # Nothing matched — infer so the product is never skipped.
    if regulation == "P":
        return "Prescription Medicines"
    for kw, cat in FALLBACK_FORMS:
        if kw in n:
            return cat
    return "General Health"


def main():
    with open(SRC, newline="", encoding="utf-8") as f:
        rows = list(csv.DictReader(f))

    counts = Counter()
    with open(REVIEW, "w", newline="", encoding="utf-8") as fr, \
         open(WOO, "w", newline="", encoding="utf-8") as fw:
        rev = csv.writer(fr)
        woo = csv.writer(fw)
        rev.writerow(["ean", "name", "regulation", "category"])
        woo.writerow(["SKU", "Categories"])
        for r in rows:
            cat = classify(r.get("name", ""), r.get("regulation", ""))
            counts[cat] += 1
            rev.writerow([r["ean"], r["name"], r["regulation"], cat])
            woo.writerow([r["ean"], cat])

    total = len(rows)
    uncat = counts.get("", 0)
    print(f"Products      : {total}")
    print(f"Categorised   : {total - uncat} ({(total - uncat) * 100 // total}%)   uncategorised: {uncat}")
    print(f"-> {os.path.basename(REVIEW)}")
    print(f"-> {os.path.basename(WOO)}")
    print("\nDistribution:")
    for cat, n in counts.most_common():
        print(f"  {n:6d}  {cat}")


if __name__ == "__main__":
    main()
