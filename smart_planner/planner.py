# -*- coding: utf-8 -*-
"""
Goal-Based AI Planner
======================
Implementasi sederhana Planning berbasis Goal-Based AI (mirip STRIPS):

- State  : himpunan fakta (fact) yang sudah dicapai oleh mahasiswa.
- Aksi   : memiliki precondition (pre) dan effect (menambah 1 fakta baru = id aksi).
- Goal   : himpunan fakta yang harus tercapai untuk profesi target.

Algoritma forward-chaining:
1. Mulai dari initial state (skill yang sudah dimiliki mahasiswa).
2. Selama goal belum tercapai, cari semua aksi yang precondition-nya sudah
   terpenuhi oleh state saat ini dan belum pernah dieksekusi -> jalankan
   (tambahkan fakta hasil aksi ke state, catat aksi ke rencana/plan).
3. Ulangi sampai goal tercapai atau tidak ada aksi baru yang bisa dijalankan
   (berarti planner berhenti / deadlock -> dilaporkan sebagai skill gap).

Hasil plan kemudian dikelompokkan menjadi 4 fase (phase) dan dipetakan
ke semester berjalan mahasiswa untuk membentuk roadmap.
"""

from data.career_data import CAREERS, SKILL_TO_FACT, PHASE_LABELS, PHASE_FOCUS_TAG, FACT_LABELS


def build_initial_state(owned_skills):
    """Ubah daftar skill checkbox (dari form) menjadi himpunan fakta awal."""
    state = set()
    for skill in owned_skills:
        fact = SKILL_TO_FACT.get(skill)
        if fact:
            state.add(fact)
    return state


def forward_chaining_plan(initial_state, actions, goal_facts):
    """
    Forward-chaining planner.
    Mengembalikan (plan, final_state, goal_tercapai: bool)
    plan = list aksi (dict) terurut sesuai eksekusi.
    """
    state = set(initial_state)
    plan = []
    remaining = {a["id"]: a for a in actions}

    changed = True
    while changed and not goal_facts.issubset(state):
        changed = False
        # urutkan berdasarkan phase agar plan cenderung mengikuti tahapan logis
        for action in sorted(remaining.values(), key=lambda a: (a["phase"], a["id"])):
            if action["id"] in state:
                continue
            if set(action["pre"]).issubset(state):
                plan.append(action)
                state.add(action["id"])
                changed = True
        # bersihkan aksi yang sudah dieksekusi dari kandidat
        remaining = {aid: a for aid, a in remaining.items() if a["id"] not in state}

    goal_reached = goal_facts.issubset(state)
    return plan, state, goal_reached


def group_plan_by_phase(plan):
    """Kelompokkan aksi hasil planning berdasarkan fase (1-4)."""
    grouped = {1: [], 2: [], 3: [], 4: []}
    for action in plan:
        grouped[action["phase"]].append(action)
    return grouped


def build_roadmap(career_key, semester_now, owned_skills):
    """
    Fungsi utama: jalankan planner dan susun roadmap per semester.
    Mengembalikan dict berisi:
      - career_label, career_description
      - skill_gap: list label fakta goal yang belum dimiliki mahasiswa di awal
      - semesters: list of {semester_no, phase, phase_label, focus_tag, actions:[...]}
      - goal_reached: bool
    """
    career = CAREERS[career_key]
    actions = career["actions"]
    goal_facts = set(career["goal"])

    initial_state = build_initial_state(owned_skills)
    plan, final_state, goal_reached = forward_chaining_plan(initial_state, actions, goal_facts)

    # skill gap = goal facts yang belum dimiliki mahasiswa sejak awal (initial_state)
    skill_gap = [FACT_LABELS.get(f, f) for f in goal_facts if f not in initial_state]

    grouped = group_plan_by_phase(plan)

    semesters = []
    sem_pointer = semester_now
    for phase in [1, 2, 3, 4]:
        phase_actions = grouped[phase]
        if not phase_actions:
            continue
        semesters.append({
            "semester_no": sem_pointer,
            "phase": phase,
            "phase_label": PHASE_LABELS[phase],
            "focus_tag": PHASE_FOCUS_TAG[phase],
            "is_now": sem_pointer == semester_now,
            "is_last": phase == max(p for p in grouped if grouped[p]),
            "actions": phase_actions,
        })
        sem_pointer += 1

    total_weeks = sum(a["weeks"] for a in plan)

    return {
        "career_key": career_key,
        "career_label": career["label"],
        "career_description": career["description"],
        "skill_gap": skill_gap,
        "semesters": semesters,
        "goal_reached": goal_reached,
        "total_weeks": total_weeks,
        "total_actions": len(plan),
    }
